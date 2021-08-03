function getCity(elem){
    searchPlacesByCityId(elem.value);
    let placeSelect = document.getElementById('journey_creation_place');
    placeSelect.innerHTML = '<option value="NULL">Sélectionnez un lieu</option>'
    let placeInfo = document.getElementById('placeInfo');
    placeInfo.innerHTML = '';
}

function searchPlacesByCityId(cityId){

    let req = new XMLHttpRequest();
    req.open("GET", "/sortir/public/call/db/places?cityId=" + cityId, true);
    req.onload = getResponseCity;
    req.send();
}

function getResponseCity(){
    let data = JSON.parse(this.responseText);

    let placeSelect = document.getElementById('journey_creation_place');

    for (let i = 0; i < data.length; i++){

        placeSelect.innerHTML += '<option value="'+ data[i].id +'">' + data[i].name + '</option>';

    }


}

function getPlaceInfo(elem){
    searchInfoByPlaceId(elem.value);
    let placeInfo = document.getElementById('placeInfo');
    placeInfo.innerHTML = '';
}

function searchInfoByPlaceId(placeId) {
    let req = new XMLHttpRequest();
    req.open("GET", "/sortir/public/call/db/info?placeId=" + placeId, true);
    req.onload = getResponseInfoPlace;
    req.send();
}

function getResponseInfoPlace(){
    let data = JSON.parse(this.responseText);

    console.log(data);

    let placeInfo = document.getElementById('placeInfo');

    placeInfo.innerHTML = '<p> Rue : <br/>'+ data[0].Street +'<br /><br /> Code postal : <br/>'+ data[0].postal +'<br /><br /> Longitude : <br/>'+ data[0].x +'<br /><br /> Latitude : <br/>'+ data[0].y +'</p>';


}