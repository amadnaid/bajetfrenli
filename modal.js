function openPlace(id){

    fetch(
        "get_place.php?id=" + id
    )

    .then(response => response.text())

    .then(data => {

        document.getElementById(
            "modalContent"
        ).innerHTML = data;

        document.getElementById(
            "placeModal"
        ).classList.add("active");

    });

}

function closePlace(){

    document.getElementById(
        "placeModal"
    ).classList.remove("active");

}

function openCategory(categoryId){

    fetch(
        "get_category.php?id=" + categoryId
    )

    .then(response => response.text())

    .then(data => {

        document.getElementById(
            "categoryContent"
        ).innerHTML = data;

        document.getElementById(
            "categoryModal"
        ).classList.add("active");

    });

}

function closeCategory(){

    document.getElementById(
        "categoryModal"
    ).classList.remove("active");

}