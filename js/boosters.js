const dropdownHeader =
    document.getElementById("dropdownHeader");

const dropdownMenu =
    document.getElementById("dropdownMenu");


document.addEventListener("click", function(e){

    const dropdown =
        document.getElementById("boosterDropdown");

    if (!dropdown.contains(e.target)) {

        dropdownMenu.classList.remove("open");
    }

});


dropdownHeader.addEventListener("click", (e) => {

    e.stopPropagation();

    dropdownMenu.classList.toggle("open");

});


document.querySelectorAll(".series-title")
.forEach(title => {

    title.addEventListener("click", () => {

        const items =
            title.nextElementSibling;

        const arrow =
            title.querySelector(".arrow");

        items.classList.toggle("open");

        arrow.textContent =
            items.classList.contains("open")
                ? "▼"
                : "▶";
    });

});

window.selectCustomBooster = function(booster){

    if (hasUnsavedChanges) {

        const confirmLeave =
            confirm(
                "Tens alterações não guardadas. Continuar?"
            );

        if (!confirmLeave) return;
    }

    window.location.href =
        "index.php?booster=" +
        encodeURIComponent(booster);
}

window.selectBoosterFromImage = function(code) {

    if (hasUnsavedChanges) {
        const confirmLeave = confirm("Tens alterações não guardadas. Queres sair sem guardar?");
        if (!confirmLeave) return;
    }


    window.location.href =
            "index.php?booster=" + encodeURIComponent(code);

}



window.resetBooster = function() {
    if (hasUnsavedChanges) {
        if (!confirm("Tens alterações por guardar. Sair mesmo?"))
            { return;
        }    
    }

    window.location.href = "index.php";
}



















