/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

// start the Stimulus application
import "./bootstrap";

// preserve scroll position
(function () {
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(";").shift();
    }

    if (getCookie("scroll") !== null) {
        window.scrollTo({ top: parseFloat(getCookie("scroll"), 10) });
    }

    const scrollButtons = document.querySelectorAll("#scroll-btn");
    for (const scrollButton of scrollButtons) {
        scrollButton?.addEventListener("click", () => {
            document.cookie = `scroll=${window.scrollY}`;
        });
    }
})();
