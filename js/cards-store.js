let cards = [];

async function loadCards(){

    if(cards.length){
        return cards;
    }

    const response =
        await fetch(
            "cards.json"
        );

    cards =
        await response.json();

    return cards;
}


async function getBoosterCards(
    booster
){

    const cards =
        await loadCards();

    return cards.filter(card =>
        card.cardnumber.startsWith(
            booster + "-"
        )
    );

}

async function getAllBoosters(){

    const cards =
        await loadCards();

    const boosters =
        new Set();

    cards.forEach(card => {

        const match =
            card.cardnumber.match(
                /^([A-Z]+[0-9]+|LM|P)/
            );

        if(match){

            boosters.add(
                match[1]
            );

        }

    });

    return [...boosters]
        .sort(
            (a,b) =>
            a.localeCompare(
                b,
                undefined,
                {
                    numeric:true
                }
            )
        );

}


async function groupBoosters(){

    const boosters =
        await getAllBoosters();

    const grouped = {};

    boosters.forEach(booster => {

        const match =
            booster.match(
                /^([A-Z]+)/
            );

        if(!match){
            return;
        }

        const prefix =
            match[1];

        if(!grouped[prefix]){
            grouped[prefix] = [];
        }

        grouped[prefix].push(
            booster
        );

    });

    return grouped;

}


function groupVariants(cards){

    const grouped = {};

    cards.forEach(card => {

        const base =
            card.base_cardnumber ||
            card.cardnumber;

        if(!grouped[base]){
            grouped[base] = [];
        }

        grouped[base].push(card);

    });

    return grouped;

}

window.groupVariants =
    groupVariants;
window.groupBoosters = groupBoosters;
window.loadCards = loadCards;
window.getBoosterCards = getBoosterCards;
window.getAllBoosters = getAllBoosters;