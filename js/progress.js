function updateProgress(selected) {
    const total = boosterTotals[selected] || 0;
    const owned = boosterOwned[selected] || 0;

    const percent = total ? (owned / total) * 100 : 0;
    const text = document.getElementById("progress-" + selected);
        if (text) {

            text.innerText =
                `${owned}/${total} (${Math.round(percent)}%)`;
        }        

    const variantTotal =
    boosterVariantTotals[selected] || 0;

    const variantOwned =
        boosterVariantOwned[selected] || 0;

    const variantPercent =
        variantTotal
            ? (variantOwned / variantTotal) * 100 : 0;

    const fill = document.getElementById("progressFill");

        fill.style.width = percent + "%";

            document.getElementById("progressText").innerText =
                `${owned}/${total} (${Math.round(percent)}%)`;

            if (percent < 25) {
                fill.style.background = "#dc3545";
            }
            else if (percent < 50) {
                fill.style.background = "#fd7e14";
            }
            else if (percent < 75) {
                fill.style.background = "#ffc107";
            }
            else if (percent < 100) {
                fill.style.background = "#28a745";
            }
            else {
                fill.style.background = "#527DF3";
        }
            
        document.getElementById("variantProgressText").innerText =
            `${variantOwned}/${variantTotal} (${Math.round(variantPercent)}%)`;

        const variantFill =
            document.getElementById("variantProgressFill");

        variantFill.style.width = variantPercent + "%";

        if (variantPercent < 25) {
            variantFill.style.background = "#dc3545";
        }
        else if (variantPercent < 50) {
            variantFill.style.background = "#fd7e14";
        }
        else if (variantPercent < 75) {
            variantFill.style.background = "#ffc107";
        }
        else if (variantPercent < 100) {
            variantFill.style.background = "#28a745";
        }
        else if (variantPercent == 100) {
            variantFill.style.background = "#527DF3";
        }

}


document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".booster-progress-fill").forEach(bar => {

        const booster = bar.dataset.booster;
        const total = boosterTotals[booster] || 0;
        const owned = boosterOwned[booster] || 0;

        const percent = total ? (owned / total) * 100 : 0;

        
        const text = document.getElementById("progress-" + booster);

        bar.style.width = percent + "%";

        // ✅ booster completo → glow
        if (percent === 100) {

    bar.style.background = "#00BFFF";
    bar.parentElement.style.boxShadow =
        "0 0 12px #00BFFF";

        }

        
        if (text) {

            text.innerHTML =
                `${owned}/${total}
                (${Math.round(percent)}%)`;
        }

    });

});













