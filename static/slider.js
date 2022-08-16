window.onload = function(){
    slideOne();
    slideTwo();
}

let sliderOne = document.getElementById("slider-1");
let sliderTwo = document.getElementById("slider-2");
let valueOne = document.getElementById("range1");
let valueTwo = document.getElementById("range2");
let minGap = 0;
let sliderTrack = document.querySelector(".slider-track");
let sliderMaxValue = document.getElementById("slider-1").max;

function slideOne(sliderOne,sliderTwo){
    if(parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap){
        sliderOne.value = parseInt(sliderTwo.value) - minGap;     
    }
    valueOne=sliderOne.parentElement.parentElement.previousElementSibling.firstChild.firstChild
    valueOne.textContent = sliderOne.value;

    valueOne.parentElement.style.width = slideOne.value.toString().lenght*8 + "px"
    console.log(slideOne.value.toString().lenght*8 + "px")
    valueOne.parentElement.style.marginLeft = sliderOne.value - valueOne.parentElement.style.width/2 + "%"
    valueOne.parentElement.style.marginRight = (sliderTwo.value-sliderOne.value)/2 - valueOne.parentElement.style.width/2 + "%"
 
    fillColor(sliderOne,sliderTwo);
}
function slideTwo(sliderTwo,sliderOne){
    if(parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap){
        sliderTwo.value = parseInt(sliderOne.value) + minGap;
    }
    valueTwo=sliderTwo.parentElement.parentElement.previousElementSibling.lastChild.firstChild
    valueTwo.textContent = sliderTwo.value;

    valueTwo.parentElement.style.marginRight = 100-sliderTwo.value - valueOne.parentElement.style.width/2 + "%"
    valueTwo.parentElement.style.marginLeft = (sliderTwo.value-sliderOne.value)/2 - valueOne.parentElement.style.width/2 + "%"

    fillColor(sliderOne,sliderTwo);
}
function fillColor(sliderOne,sliderTwo){
    //sliderOne.max=sliderTwo.max
    percent1 = (sliderOne.value / sliderOne.max) * 100;
    percent2 = (sliderTwo.value / sliderOne.max) * 100;
    //sliderTrack
    sliderOne.parentElement.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , #3264fe ${percent1}% , #3264fe ${percent2}%, #dadae5 ${percent2}%)`;
}
