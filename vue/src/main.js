import { createApp } from "vue";

// App Component
import App from "./App.vue";
// Style CSS
import "./assets/style.css";
// Routeing
import router from "./routes";
// Vuex
import store from "./store";
// FontAwesom Icons
import { useFontAwesomFun } from "./FontAwesome/fontAwesome.js";
const { FontAwesomeIcon } = useFontAwesomFun();
// Bootstrap CSS
import "bootstrap/dist/css/bootstrap.css";
import bootstrap from "bootstrap/dist/js/bootstrap.bundle.js";
// Sweet Alert2
import VueSweetalert2 from "vue-sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";

const app = createApp(App);

// Vuex Store
app.use(store);
// Bootstrap CSS
app.use(bootstrap);
// Sweet Alert2
app.use(VueSweetalert2);
// FontAwesome Icons
app.component("font-awesome-icon", FontAwesomeIcon);
//  App Routing
app.use(router);
// Default App Mount
app.mount("#app");
