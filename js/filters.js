window.filterCards = function() {
    const selected = currentBooster.toUpperCase();
    const cards = document.querySelectorAll(".card");
    const grid = document.getElementById("boosterGrid");
    const backButton = document.getElementById("backButtonContainer");
    const saveContainer = document.getElementById("boosterSaveContainer");
      
    const progressBox = document.querySelector(".progress-box");
    const searchValue =
        document
            .getElementById("searchBox")
            .value
            .toLowerCase()
            .trim();

    if (selected) {
        progressBox.style.display = "block";
    } else {
        progressBox.style.display = "none";
    }

    
    if (selected) {
        document.getElementById("boosterTitle").innerText =
            selected + " - " + (boosterNames[selected] || '');
    } else {
        document.getElementById("boosterTitle").innerText = "";
    }


    if (selected) {
        saveContainer.style.display = "block";
    } else {
        saveContainer.style.display = "none";
    }


    cards.forEach(card => {

        const booster = card.getAttribute("data-booster");

        
    let show = selected && booster === selected;


        card.style.display = (selected && booster === selected) ? "inline-block" : "none";

    const onlyVariants = document.getElementById("variantsOnly")?.classList.contains("active");

    const onlyMissing = document.getElementById("missingOnly")?.classList.contains("active");


    if (
        onlyVariants &&
        parseInt(card.dataset.variants) === 0
    ){
        show = false;
    }


    if (onlyMissing) {

        const total =
            parseInt(
                card.querySelector(".card-total")
                    ?.innerText || 0
            );

        if (total > 0) {
            show = false;
        }
    }


        const cardName =
            card.querySelector("h3")
                .innerText
                .toLowerCase();

        const cardId =
            card.querySelector("p")
                .innerText
                .toLowerCase();

        if (
            searchValue &&
            !cardName.includes(searchValue) &&
            !cardId.includes(searchValue)
        ){
            show = false;
        }
        
    card.style.display =
        show
            ? "inline-block"
            : "none";

    });

    if (selected) {
        window.scrollTo(0, 0);
        grid.classList.add("hidden");
     
        document.getElementById("boosterSide").innerHTML =
            selected ? `<img src="images/boosters/${selected}.jpg" loading="lazy">` : "";

        if (backButton) backButton.style.display = 'block';
    } else {
        grid.classList.remove("hidden");
        if (backButton) backButton.style.display = 'none';
    }

    
        if (selected) {
                saveContainer.style.display = 'block';
            }
        else {
                saveContainer.style.display = 'none';
            }
            
        if (selected) {
            updateProgress(selected);
        }


}





    const missingOnly =
        document.getElementById("missingOnly");

    if (missingOnly) {
        missingOnly.addEventListener(
            "change",
            filterCards
        );
    }


    const variantsOnly =
        document.getElementById("variantsOnly");

    if (variantsOnly) {
        variantsOnly.addEventListener(
            "change",
            filterCards
        );
}



    const searchBox =
        document.getElementById("searchBox");

    if (searchBox) {

        searchBox.addEventListener(
            "input",
            function () {

                filterCards();
            }
        );

    }


    document
        .querySelectorAll(".filter-pill")
        .forEach(btn => {

            btn.addEventListener("click", () => {

                btn.classList.toggle("active");

                filterCards();

            });

        });






