<?php

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSurveyQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SurveyQuestion::class, 'survey_question_id');
            $table->foreignIdFor(SurveyAnswer::class, 'survey_answer_id');
            $table->text('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_question_answers');
    }
}