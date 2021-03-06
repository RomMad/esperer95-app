// Message flash
export default class MessageFlash {

    constructor(alert, message) {
        this.msgFlashContentElt = document.getElementById("js-notif-container");
        this.alert = alert;
        this.message = message;
        this.msg = null;
        this.time = 0;
        this.init();
    }

    // Initialise le message
    init() {
        this.msg = document.createElement("div");
        this.msg.className = "msg-content rounded";
        this.msg.innerHTML =
            `<div id="js-msg-flash" class="msg-flash alert alert-${this.alert} alert-dismissible fade show align-items-center"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div>${this.message}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <span id="timeline" class="bg-${this.alert}"></span>
            </div>`
        this.timerID = setInterval(this.timer.bind(this), 1000);
        this.msgFlashContentElt.insertBefore(this.msg, this.msgFlashContentElt.firstChild);
    }

    timer() {
        if (this.time > 10) {
            clearInterval(this.timerID);
            this.msg.remove();
        }
        this.time++;
    }
}