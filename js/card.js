window.changeAmount = function(cardnumber, delta) {
    const input = document.getElementById("amount-" + cardnumber);
    let value = parseInt(input.value) || 0;
    value = Math.max(0, value + delta);
    input.value = value;
    updateVisualState(cardnumber);
}


window.updateVisualState = function(cardnumber) {


    hasUnsavedChanges = true;
    updateProgress(currentBooster);


    const btn = document.querySelector('.save-btn');
    const label = document.querySelector('.save-label');

    btn.classList.remove('green');
    btn.classList.add('red');

    label.classList.remove('green');
    label.classList.add('red');

    const input = document.getElementById("amount-" + cardnumber);
    const card = document.getElementById("card-" + cardnumber);
        

    const base = input.dataset.base;

    if (base) {
        updateGroupTotal(base);
    }    
    
    if (input.classList.contains("variant-input"))

    {
            updateVariantBadge(base);
        }


    if (card) {
        if (parseInt(input.value) > 0) {
            card.classList.add("owned");
        } else {
            card.classList.remove("owned");
        }

    }


}


function updateGroupTotal(baseId) {


    let total = 0;

    // ✅ soma variantes
    document.querySelectorAll(`.variant-input[data-base="${baseId}"]`)
        .forEach(input => {
            total += parseInt(input.value) || 0;
        });

    // ✅ soma base
    const baseInput = document.querySelector(`.main-input[data-base="${baseId}"]`);
    let baseValue = parseInt(baseInput.value) || 0;

    total += baseValue;

    // ✅ mostrar total num campo extra (opcional)  
    const totalDisplay = document.querySelector(`#total-${baseId}`);

    if (totalDisplay) {
        totalDisplay.innerText = total;

        if (total > 0) {
            totalDisplay.classList.add('has-value');
        } else {
            totalDisplay.classList.remove('has-value');
        }
    }

    const baseDisplay =
    document.getElementById(
        "base-count-" + baseId
    );

    if (baseDisplay) {

        baseDisplay.innerText =
            "Base: " + baseValue;

    }

}

function updateVariantBadge(baseId) {

    const badge =
        document.getElementById(
            "variant-badge-" + baseId
        );

    if (!badge) return;

    const variants =
        document.querySelectorAll(
            `.variant-input[data-base="${baseId}"]`
        );

    let ownedVariants = 0;

    variants.forEach(input => {

        if (
            (parseInt(input.value) || 0) > 0
        ) {
            ownedVariants++;
        }

    });


    const totalVariants =
        parseInt(
            badge.dataset.total
        );


    badge.classList.remove(
        "owned-variant",
        "complete-variant"
    );


    if (
        ownedVariants === totalVariants &&
        totalVariants > 0
    ) {

        badge.classList.add(
            "complete-variant"
        );

    }
    
    else if (ownedVariants > 0) {

        badge.classList.add(
            "owned-variant"
        );

    }


    const text =
        badge.querySelector("span:last-child");

    if (text) {

        text.innerText =
            ownedVariants +
            "/" +
            totalVariants +
            " Alt Art";

    }

}


window.addEventListener('DOMContentLoaded', () => {

document.querySelectorAll('.card').forEach(card => {

    card.addEventListener('click', function (e) {

        if (e.target.closest('.controls')) return;
        if (e.target.closest('.variant-item')) return;
        if (e.target.closest('#imageModal')) return;

        const alreadyOpen =
            this.classList.contains('active');

        document.querySelectorAll('.card').forEach(c =>
            c.classList.remove('active'));

        if (!alreadyOpen) {
            this.classList.add('active');
        }
    });

});

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.card')) {
        document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
        }
    });

});


document.querySelectorAll('.card').forEach(card => {

    card.addEventListener('click', function () {

        const panel = this.querySelector('.variants-panel');

        if (!panel) return;

        panel.classList.remove('left-side');

        const rect = this.getBoundingClientRect();

        const spaceRight =
            window.innerWidth - rect.right;

        if (spaceRight < 260) {
            panel.classList.add('left-side');
        }
    });

});


window.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.card').forEach(card => {
        const base = card.dataset.base;

        if (base) {
            updateGroupTotal(base);
        }
    });

});






























