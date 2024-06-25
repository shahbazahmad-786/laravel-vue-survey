import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

/* Import Solid Icons */
import {
  faUser,
  faLock,
  faUserCircle,
  faEdit,
  faEye,
  faDeleteLeft,
  faImage,
  faPlus,
  faTrash,
} from "@fortawesome/free-solid-svg-icons";
/* Import Brands Icons */
// import { faTwitter } from "@fortawesome/free-brands-svg-icons";

/* add icons to the library */
library.add(faUser, faLock, faUserCircle, faEdit, faEye, faDeleteLeft, faImage, faPlus, faTrash);
export const useFontAwesomFun = () => {
  return { FontAwesomeIcon };
};
