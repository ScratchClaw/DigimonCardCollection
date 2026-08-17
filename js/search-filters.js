function updateResultCount(){

    const visible =
        document
            .querySelectorAll(
                '.search-card:not([style*="none"])'
            ).length;

    document
        .getElementById("resultCount")
        .innerText =
            visible +
            " resultado(s) encontrado(s)";
}


function filterSearchCards() {

    const onlyMissing =
        document.getElementById("missingOnly")
            ?.classList.contains("active");

    const onlyAlt =
        document.getElementById("altOnly")
            ?.classList.contains("active");

    const activeBoosters =
    Array.from(
        document.querySelectorAll(
            ".booster-pill.active"
        )
    )
    .map(btn => btn.dataset.booster);

    document
        .querySelectorAll(".search-card")
        .forEach(card => {

            let show = true;

            const owned =
                card.dataset.owned === "1";

            const variant =
                card.dataset.variant === "1";

            const booster =
                card.dataset.booster;

            if (
                onlyMissing &&
                owned
            ){
                show = false;
            }

            if (
                onlyAlt &&
                !variant
            ){
                show = false;
            }

            if (activeBoosters.length > 0) {

                const boosterMatch =
                    activeBoosters.some(prefix =>
                        booster.startsWith(prefix)
                    );

                if (!boosterMatch) {
                    show = false;
                }

            }

            card.style.display =
                show
                    ? "block"
                    : "none";
        });

    updateResultCount();
}


document
    .querySelectorAll(".filter-pill")
    .forEach(btn => {

        btn.addEventListener("click", () => {

            btn.classList.toggle("active");

            filterSearchCards();

        });

    });











