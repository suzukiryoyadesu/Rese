const formInputName = document.forms.form.name; //document.formsでformの中身を取得,さらに後ろの記述で細かく指定できる

formInputName.addEventListener('input',()=>{     //formInputDateにinputがされたら
    let inputName = document.getElementById('input_name');
    inputName.textContent = formInputName.value
})

const formInputImage = document.forms.form.image;

formInputImage.addEventListener('change', (event) => {
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.addEventListener('load', (event) => {
        var inputImage = document.getElementById('input_image');
        inputImage.src = event.target.result;
    });
})

const formInputArea = document.forms.form.area_id;

formInputArea.addEventListener('input',()=>{
    let inputArea = document.getElementById('input_area');
    inputArea.textContent = '#' + formInputArea.options[formInputArea.selectedIndex].textContent
})

const formInputGenre = document.forms.form.genre_id;

formInputGenre.addEventListener('input',()=>{
    let inputGenre = document.getElementById('input_genre');
    inputGenre.textContent = '#' + formInputGenre.options[formInputGenre.selectedIndex].textContent
})

const formInputDetail = document.forms.form.detail;

formInputDetail.addEventListener('input',()=>{
    let inputDetail = document.getElementById('input_detail');
    inputDetail.innerText = formInputDetail.value;
})