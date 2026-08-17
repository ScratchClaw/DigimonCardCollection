window.hasUnsavedChanges = false;


window.goBack = function() {

    if (hasUnsavedChanges) {

        if (
            !confirm(
                "Tens alterações por guardar. Sair mesmo?"
            )
        ) {
            return;
        }
    }

    window.location.href =
        "index.php";
}


window.addEventListener(
    "beforeunload",
    function (e) {

        if (hasUnsavedChanges) {

            e.preventDefault();
            e.returnValue = '';

        }

    }
);


window.addEventListener("DOMContentLoaded", () => {

    const url =
        new URLSearchParams(
            window.location.search
        );

    if (url.get("saved")) {

        hasUnsavedChanges = false;

        const btn =
            document.querySelector(".save-btn");

        if (btn) {

            btn.classList.remove("red");
        }
    }
});









