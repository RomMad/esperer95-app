import ValidationPerson from "./validationPerson";
import MessageFlash from "../utils/messageFlash";
import Loader from "../utils/loader";

// Requête Ajax pour mettre à jour les informations individuelles
export default class UpdatePerson {

    constructor(ajaxRequest) {
        this.ajaxRequest = ajaxRequest;
        this.personElt = document.querySelector('form[name=person]');
        this.updatePersonBtn = document.getElementById("updatePerson");
        this.url = this.updatePersonBtn.getAttribute("data-url");
        this.loader = new Loader();
        this.init();
    }

    init() {
        let validationPerson = new ValidationPerson(
            "person_lastname",
            "person_firstname",
            "person_birthdate",
            "person_gender",
            "person_email"
        );

        this.updatePersonBtn.addEventListener("click", function (e) {
            e.preventDefault();
            if (!validationPerson.getNbErrors()) {
                this.loader.on();
                let formData = new FormData(this.personElt);
                let formToString = new URLSearchParams(formData).toString();
                this.ajaxRequest.init("POST", this.url, this.response.bind(this), true, formToString);
            } else {
                new MessageFlash("danger", "Veuillez corriger les erreurs avant de mettre à jour.");
            }
        }.bind(this));
    }

    response(data) {
        let dataJSON = JSON.parse(data);

        if (dataJSON.code === 200) {
            if (dataJSON.alert === "success") {
                document.getElementById("js-person-updated").textContent = "(modifié le " + dataJSON.date + " par " + dataJSON.user + ")";
            }
        }
        this.loader.off();
        new MessageFlash(dataJSON.alert, dataJSON.msg);
    }
}