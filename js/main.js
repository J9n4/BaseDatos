

document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ JS Cargado y listo");


    const botonesAccion = document.querySelectorAll('.btn-action');
    
    botonesAccion.forEach(btn => {
        btn.addEventListener('click', (e) => {
            
            e.preventDefault(); 
            
            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            
            if (targetForm) {
                console.log("Abriendo formulario:", targetId);

               
                document.querySelectorAll('.form-section').forEach(f => {
                    f.style.display = 'none';
                });


                targetForm.style.display = 'block';
                
                
                targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

            } else {
                console.error("❌ Error: No existe un formulario con ID: " + targetId);
            }
        });
    });



    const tabla = document.querySelector('table');
    
    if (tabla) {
        tabla.addEventListener('click', (e) => {
            
            
            const btnEdit = e.target.closest('.btn-row-edit');
            if (btnEdit) {
                e.preventDefault();
                
                try {
                    
                    const data = JSON.parse(btnEdit.getAttribute('data-json'));
                    
                    
                    Object.keys(data).forEach(key => {
                        const input = document.getElementById('mod_' + key);
                        if (input) {
                            input.value = data[key];
                        }
                    });

                   
                    mostrarSolo('form-modificar');

                } catch (err) {
                    console.error("Error al leer datos JSON:", err);
                }
            }

           
            const btnDel = e.target.closest('.btn-row-delete');
            if (btnDel) {
                e.preventDefault();
                const id = btnDel.getAttribute('data-id');
                
                
                const inputDel = document.getElementById('del_id');
                if(inputDel) {
                    inputDel.value = id;
                }

               
                mostrarSolo('form-eliminar');
            }
        });
    }

    function mostrarSolo(formId) {
       
        document.querySelectorAll('.form-section').forEach(f => f.style.display = 'none');
        
        
        const form = document.getElementById(formId);
        if(form) {
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth' });
        }
    }

});