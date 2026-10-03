let question= document.querySelectorAll(".question")
question.forEach(function(question))
question.addEventListener("click",function(){
    let answer = this.nextElementsibling;
    answer.classList.toggle("show");

    
});
})

