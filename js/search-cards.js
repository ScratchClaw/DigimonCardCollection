window.changeAmount = function(cardnumber, delta) {

    const input =
        document.getElementById(
            "amount-" + cardnumber
        );

    let value =
        parseInt(input.value) || 0;

    value =
        Math.max(0, value + delta);

    input.value = value;

    const card =
        input.closest(".search-card");

    const badge =
        card.querySelector(
            ".owned-badge, .missing-badge"
        );

    if (value > 0) {

        card.classList.add("owned");

        badge.classList.remove(
            "missing-badge"
        );

        badge.classList.add(
            "owned-badge"
        );

        badge.innerText =
            "✓ Tenho";

    } else {

        card.classList.remove("owned");

        badge.classList.remove(
            "owned-badge"
        );

        badge.classList.add(
            "missing-badge"
        );

        badge.innerText =
            "✗ Falta";
    }

    hasUnsavedChanges = true;

    document
        .querySelector(".save-btn")
        .classList.add("red");
}