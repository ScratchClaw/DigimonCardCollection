const DB_NAME = "DigimonCollection";
const DB_VERSION = 1;
const STORE_NAME = "cards";

function openDB() {

    return new Promise((resolve, reject) => {

        const request =
            indexedDB.open(
                DB_NAME,
                DB_VERSION
            );

        request.onupgradeneeded = function(event) {

            const db =
                event.target.result;

            if (
                !db.objectStoreNames.contains(
                    STORE_NAME
                )
            ) {

                db.createObjectStore(
                    STORE_NAME,
                    {
                        keyPath: "cardnumber"
                    }
                );

            }

        };

        request.onsuccess = function() {

            resolve(request.result);

        };

        request.onerror = function() {

            reject(request.error);

        };

    });

}

async function saveCardAmount(
    cardnumber,
    amount
){

    const db =
        await openDB();

    const tx =
        db.transaction(
            STORE_NAME,
            "readwrite"
        );

    const store =
        tx.objectStore(
            STORE_NAME
        );

    store.put({
        cardnumber,
        amount
    });

}


async function getCardAmount(
    cardnumber
){

    const db =
        await openDB();

    return new Promise(resolve => {

        const tx =
            db.transaction(
                STORE_NAME,
                "readonly"
            );

        const store =
            tx.objectStore(
                STORE_NAME
            );

        const request =
            store.get(cardnumber);

        request.onsuccess = () => {

            resolve(
                request.result
                    ?.amount ?? 0
            );

        };

    });

}


async function getAllCards() {

    const db =
        await openDB();

    return new Promise((resolve, reject) => {

        const tx =
            db.transaction(
                STORE_NAME,
                "readonly"
            );

        const store =
            tx.objectStore(
                STORE_NAME
            );

        const request =
            store.getAll();

        request.onsuccess = () => {
            resolve(
                request.result
            );
        };

        request.onerror = () => {
            reject(
                request.error
            );
        };

    });

}


async function exportCollection() {

    const cards =
        await getAllCards();

    const blob =
        new Blob(
            [
                JSON.stringify(
                    cards,
                    null,
                    2
                )
            ],
            {
                type:"application/json"
            }
        );

    const url =
        URL.createObjectURL(
            blob
        );

    const a =
        document.createElement("a");

    a.href = url;

    a.download =
        "digimon_collection_backup.json";

    a.click();

    URL.revokeObjectURL(
        url
    );

}


async function importCollection(file) {

    const text =
        await file.text();

    const cards =
        JSON.parse(text);

    const db =
        await openDB();

    const tx =
        db.transaction(
            STORE_NAME,
            "readwrite"
        );

    const store =
        tx.objectStore(
            STORE_NAME
        );

    cards.forEach(card => {

        store.put({
            cardnumber:
                card.cardnumber,

            amount:
                card.amount
        });

    });

    return new Promise(resolve => {

        tx.oncomplete = () => {
            resolve();
        };

    });

}





window.openDB = openDB;
window.saveCardAmount = saveCardAmount;
window.getCardAmount = getCardAmount;
window.exportCollection =  exportCollection;
window.importCollection =
    importCollection;