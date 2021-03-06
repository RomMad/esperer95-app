import ValidationSupport from "./validationSupport";
import RemoveSupportPerson from "./removeSupportPerson";

import "select2";
import "../utils/maskDeptCode";
import "../utils/maskPhone";

new ValidationSupport();
new RemoveSupportPerson();

$("select.multi-select").select2({
    // theme: "bootstrap4",
    placeholder: "-- Service --",
});

document.querySelectorAll("div.card-header").forEach(cardHeaderElt => {
    let spanFaElt = cardHeaderElt.querySelector("span.fa");
    cardHeaderElt.addEventListener("click", function () {
        if (cardHeaderElt.classList.contains("collapsed")) {
            spanFaElt.classList.replace("fa-chevron-right", "fa-chevron-down");
        } else {
            spanFaElt.classList.replace("fa-chevron-down", "fa-chevron-right");
        }
    });
});