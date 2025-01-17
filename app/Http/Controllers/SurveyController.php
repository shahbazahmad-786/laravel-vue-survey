<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\SurveyAnswer;
use Illuminate\Http\Request;
use App\Models\SurveyQuestion;
use Illuminate\Validation\Rule;
use App\Models\SurveyQuestionAnswer;
use Illuminate\Support\Facades\File;
use App\Http\Resources\SurveyResource;
use App\Http\Requests\StoreSurveyRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Requests\StoreSurveyAnswerRequest;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return SurveyResource::collection(
            Survey::where('user_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->paginate(3)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyRequest $request)
    {
        $data = $request->validated();

        /* Check if image was given and save on local file system */
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']); /* (!) Check SaveImage Function Below */
            $data['image'] = $relativePath;
        }

        /* Create new Survey */
        $survey = Survey::create($data);

        /* Create new questions */
        foreach ($data['questions'] as $question) {
            $question['survey_id'] = $survey->id;
            $this->createQuestion($question); /* (!) Check createQuestion Function Below */
        }

        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey, Request $request)
    {
        /* Check User Authentication */
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Survey $survey
     * @return \Illuminate\Http\Response
     */
    public function showForGuest(Survey $survey)
    {
        // if (!$survey->status) {
        //     return response("", 404);
        // }

        // $currentDate = new \DateTime();
        // $expireDate = new \DateTime($survey->expire_date);
        // if ($currentDate > $expireDate) {
        //     return response("", 404);
        // }

        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        $data = $request->validated();

        /* Check if image was given and save on local file system */
        if (isset($data['image'])) {
            $relativePath = $this->saveImage($data['image']); /* (!) Check saveImage Function Below */
            $data['image'] = $relativePath;

            /* If there is an old image, delete it */
            if ($survey->image) {
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }

        /* Update survey in the database */
        $survey->update($data);

        /** 
         * Start Update Question 
         **/

        // Get ids as plain array of existing questions
        $existingIds = $survey->questions()->pluck('id')->toArray();
        // Get ids as plain array of new questions
        $newIds = Arr::pluck($data['questions'], 'id');
        // Find questions to delete
        $toDelete = array_diff($existingIds, $newIds);
        //Find questions to add
        $toAdd = array_diff($newIds, $existingIds);

        // Delete questions by $toDelete array
        SurveyQuestion::destroy($toDelete);

        /* Create new questions */
        foreach ($data['questions'] as $question) {
            if (in_array($question['id'], $toAdd)) {
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question); /* (!) Check createQuestion Function Below */
            }
        }

        /* Update existing questions */
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach ($survey->questions as $question) {
            if (isset($questionMap[$question->id])) {
                $this->updateQuestion($question, $questionMap[$question->id]); /* (!) Check updateQuestion Function Below */
            }
        }

        /** 
         * End Update Question 
         **/


        return new SurveyResource($survey);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Request $request)
    {
        /* Check User Authentication */
        $user = $request->user();
        if ($user->id !== $survey->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        $survey->delete();

        /* If there is an old image, delete it */
        if ($survey->image) {
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }

        return response('', 204);
    }

    /**
     * Save image in local file system and return saved image path
     *
     * @param $image
     * @throws \Exception
     * @author Shahbaz Ahmad <shahbazahmad0987654321@gmail.com>
     */
    private function saveImage($image)
    {
        /* Check if image is valid base64 string */
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $type)) {
            /* Take out the base64 encoded text without mime type */
            $image = substr($image, strpos($image, ',') + 1);
            /* Get file extension */
            $type = strtolower($type[1]); /* jpg, png, gif */

            /* Check if file is an image */
            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new \Exception('invalid image type');
            }
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            if ($image === false) {
                throw new \Exception('base64_decode failed');
            }
        } else {
            throw new \Exception('did not match data URI with image data');
        }

        $dir = 'images/'; /* !create new folder /public/images & add .gitignore with text * !.gitignore */
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        if (!File::exists($absolutePath)) {
            File::makeDirectory($absolutePath, 0755, true);
        }
        file_put_contents($relativePath, $image);

        return $relativePath;
    }

    /**
     * Create a question and return
     *
     * @param $data
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     * @author Shahbaz Ahmad <shahbazahmad0987654321@gmail.com>
     */
    private function createQuestion($data)
    {
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description' => 'nullable|string',
            'data' => 'present',
            'survey_id' => 'exists:App\Models\Survey,id'
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    /**
     * Update a question and return true or false
     *
     * @param \App\Models\SurveyQuestion $question
     * @param                            $data
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     * @author Shahbaz Ahmad <shahbazahmad0987654321@gmail.com>
     */
    private function updateQuestion(SurveyQuestion $question, $data)
    {
        if (is_array($data['data'])) {
            $data['data'] = json_encode($data['data']);
        }
        $validator = Validator::make($data, [
            'id' => 'exists:App\Models\SurveyQuestion,id',
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description' => 'nullable|string',
            'data' => 'present',
        ]);

        return $question->update($validator->validated());
    }

    public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey)
    {
        $validated = $request->validated();
        //    var_dump($validated, $survey);

        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        ]);

        foreach ($validated['answers'] as $questionId => $answer) {
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();
            if (!$question) {
                return response("Invalid question ID: \"$questionId\"", 400);
            }

            $data = [
                'survey_question_id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer
            ];

            $questionAnswer = SurveyQuestionAnswer::create($data);
        }

        return response("", 201);
    }

    public function createQuestionAnswer($data)
    {
        if (is_array($data['answer'])) {
            $data['answer'] = json_encode($data['answer']);
        }
    }
}
