let SoundEffect = {
    getSoundState: function () {
        return parseInt(sessionStorage.getItem("soundState"));
    },
    "setSoundState": function () {
        let sound_state = self.getSoundState() ? 0 : 1;
        sessionStorage.setItem("soundState", sound_state);
    },
    playAudio: function ($media, $auto, $type) {
        if ( this.getSoundState() > 0 )
        {
            return;
        }
        let sound, media_src = "static/sound/" + $media;
        if ( !$auto )
        {
            sound = new Audio();
            sound.type = ($type || "audio/mpeg");
            sound.allow = "autoplay";
            sound.src = media_src;
            sound.play();
            return;
        }
        sound = document.createElement('iframe');
        sound.allow = "autoplay";
        sound.style.display = "none";
        sound.id = new Date().getTime();
        sound.src = media_src;
        document.body.appendChild(sound);
        setTimeout(function () {
            sound.remove();
        }, 2000);
    },
    "navClick": function () {
        this.playAudio("navigate_1.mp3");
    },
    "wrongAlert": function () {
        this.playAudio("error.mp3");
    },
    "buttonClick": function () {
        this.playAudio("click-me.mp3");
    },
    "successAlert": function () {
        this.playAudio("success.mp3")
    },
    sendAlert: function () {
        this.playAudio("notify_2.mp3", true);
    },
    "_sendAlert": function () {
        let hideButton = document.createElement("button");
        //hideButton.setAttribute('id', "hide-btn");
        hideButton.innerHTML = "";
        hideButton.style.display = "none";
        hideButton.onclick = () => {

        };
        document.body.appendChild(hideButton);
        setTimeout(function () {
            hideButton.onclick();
            hideButton.remove();
        }, 500);
        return true;
    }
};

//export default SoundEffect;