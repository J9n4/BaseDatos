/* js/main.js - Archivo Completo y Corregido */

document.addEventListener('DOMContentLoaded', () => {
    console.log("✅ JS Cargado y listo");

    // =======================================================
    // 1. LÓGICA PARA BOTONES SUPERIORES (Agregar, Modificar, Eliminar)
    // =======================================================
    const botonesAccion = document.querySelectorAll('.btn-action');
    
    botonesAccion.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // EL CAMBIO IMPORTANTE: Prevenir comportamiento por defecto
            e.preventDefault(); 
            
            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            
            if (targetForm) {
                console.log("Abriendo formulario:", targetId);

                // 1. Ocultamos TODOS los formularios primero
                document.querySelectorAll('.form-section').forEach(f => {
                    f.style.display = 'none';
                });

                // 2. Mostramos SIEMPRE el formulario seleccionado
                // (Quitamos la lógica de 'toggle' para evitar errores de rebote)
                targetForm.style.display = 'block';
                
                // 3. Hacemos scroll suave hacia el formulario
                targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });

            } else {
                console.error("❌ Error: No existe un formulario con ID: " + targetId);
            }
        });
    });


    // =======================================================
    // 2. LÓGICA PARA BOTONES DENTRO DE LA TABLA (Lápiz y Basurero)
    // =======================================================
    const tabla = document.querySelector('table');
    
    if (tabla) {
        tabla.addEventListener('click', (e) => {
            
            // --- A) CLICK EN EDITAR (LÁPIZ) ---
            const btnEdit = e.target.closest('.btn-row-edit');
            if (btnEdit) {
                e.preventDefault();
                
                try {
                    // Leemos el JSON guardado en el botón
                    const data = JSON.parse(btnEdit.getAttribute('data-json'));
                    
                    // Rellenamos los inputs buscando por id="mod_nombreColumna"
                    Object.keys(data).forEach(key => {
                        const input = document.getElementById('mod_' + key);
                        if (input) {
                            input.value = data[key];
                        }
                    });

                    // Mostramos el formulario de Modificar
                    mostrarSolo('form-modificar');

                } catch (err) {
                    console.error("Error al leer datos JSON:", err);
                }
            }

            // --- B) CLICK EN ELIMINAR (BASURERO) ---
            const btnDel = e.target.closest('.btn-row-delete');
            if (btnDel) {
                e.preventDefault();
                const id = btnDel.getAttribute('data-id');
                
                // Ponemos el ID en el input oculto del formulario eliminar
                const inputDel = document.getElementById('del_id');
                if(inputDel) {
                    inputDel.value = id;
                }

                // Mostramos el formulario de Eliminar
                mostrarSolo('form-eliminar');
            }
        });
    }

    // =======================================================
    // 3. FUNCIONES AUXILIARES
    // =======================================================
    function mostrarSolo(formId) {
        // Oculta todos
        document.querySelectorAll('.form-section').forEach(f => f.style.display = 'none');
        
        // Muestra el deseado
        const form = document.getElementById(formId);
        if(form) {
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth' });
        }
    }

});