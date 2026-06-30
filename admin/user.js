function loadUsers(page = 1){

    document.getElementById("loading").style.display="block";

    let search=document.getElementById("search").value;

    let role=document.getElementById("role").value;

    fetch(
        "ajax_users.php?search="
        +encodeURIComponent(search)
        +"&role="
        +encodeURIComponent(role)
        +"&page="
        +page
    )

    .then(response=>response.text())

    .then(data=>{

        document.getElementById("usersTable").innerHTML=data;

        document.getElementById("loading").style.display="none";

    });

}

document.addEventListener("change",function(e){

    if(e.target.id==="checkAll"){

        document.querySelectorAll(".userCheck").forEach(function(box){

            box.checked=e.target.checked;

        });

    }

});

function deleteSelectedUsers(){

    let ids=[];

    document.querySelectorAll(".userCheck:checked").forEach(function(box){

        ids.push(box.value);

    });

    if(ids.length===0){

        alert("Select at least one user.");

        return;

    }

    if(!confirm("Delete selected users?")){

        return;

    }

    let formData=new FormData();

    ids.forEach(function(id){

        formData.append("ids[]",id);

    });

    fetch("delete_users.php",{

        method:"POST",

        body:formData

    })

    .then(response=>response.text())

    .then(data=>{

        if(data.trim()=="success"){

            alert("Users deleted successfully.");

            loadUsers();

        }else{

            alert(data);

        }

    });

}