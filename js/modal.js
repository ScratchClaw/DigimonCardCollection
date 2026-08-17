document.querySelectorAll(".card img").forEach(img => {
    img.addEventListener("click", function(event) {
        event.stopPropagation();
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("imageModalContent");
        modal.style.display = "flex";
        modalImg.src = this.src;
    });
});

function closeModal() {
    document.getElementById("imageModal").style.display = "none";
}
