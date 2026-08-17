const CACHE_NAME =
    "digimon-v1";

const FILES_TO_CACHE = [

    "./",

    "./test-indexeddb.html",

    "./cards.json",

    "./manifest.json",

    "./js/indexeddb.js",

    "./js/cards-store.js"

];

self.addEventListener(
    "install",
    event => {

        event.waitUntil(

            caches
            .open(CACHE_NAME)
            .then(cache => {

                return cache.addAll(
                    FILES_TO_CACHE
                );

            })

        );

    }
);

self.addEventListener(
    "fetch",
    event => {

        event.respondWith(

            caches.match(
                event.request
            )
            .then(response => {

                return (
                    response ||
                    fetch(event.request)
                );

            })

        );

    }
);