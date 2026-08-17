    window.hasUnsavedChanges = false;


    window.addEventListener("beforeunload", function (e) {

        if (!hasUnsavedChanges) {
            return;
        }

        e.preventDefault();
        e.returnValue = '';
    });


    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById("cardSaveForm").addEventListener("submit", function () {
            hasUnsavedChanges = false;
                
            const btn = document.querySelector('.save-btn');
            if (btn) btn.classList.add('green');
            const label = document.querySelector('.save-label');

            btn.classList.remove('red');
            btn.classList.add('green');

            label.classList.remove('red');
            label.classList.add('green');
        });
    });


    window.addEventListener('load', () => {

        document.getElementById("selectedBoosterInput").value =
            currentBooster;

        filterCards();

    });


  window.addEventListener('DOMContentLoaded', () => {

  
    const btn = document.querySelector('.save-btn');
    const label = document.querySelector('.save-label');

    document.getElementById("boosterSaveContainer").style.display = "none";

    btn.classList.add('green');
    label.classList.add('green');


    const popup = document.getElementById('popupMessage');
    if (popup) {
      setTimeout(() => {
        popup.style.opacity = '0';
        setTimeout(() => popup.remove(), 500);
      }, 3000);

      // Optional: remove `saved=1` from URL without reloading
      const url = new URL(window.location);
      url.searchParams.delete('saved');
      window.history.replaceState({}, document.title, url);
    }
  });














