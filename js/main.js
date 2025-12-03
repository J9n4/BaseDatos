/* js/main.js */
document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. LÓGICA DE TOGGLE (Abrir/Cerrar Formularios) ---
    document.querySelectorAll('.btn-action').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            
            // Verificamos si ya está visible
            const isVisible = targetForm.style.display === 'block';
            
            // Ocultamos TODOS los formularios primero
            document.querySelectorAll('.form-section').forEach(f => f.style.display = 'none');

            // Si no estaba visible, lo mostramos ahora
            if (!isVisible) {
                targetForm.style.display = 'block';
                // Scroll suave hacia el formulario
                targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // --- 2. LÓGICA GENÉRICA PARA LA TABLA (Editar y Eliminar) ---
    const tabla = document.querySelector('table');
    
    if (tabla) {
        tabla.addEventListener('click', (e) => {
            
            // A) Click en Editar (Lápiz)
            const btnEdit = e.target.closest('.btn-row-edit');
            if (btnEdit) {
                const data = JSON.parse(btnEdit.getAttribute('data-json'));
                rellenarFormularioEditar(data);
            }

            // B) Click en Eliminar (Basurero)
            const btnDel = e.target.closest('.btn-row-delete');
            if (btnDel) {
                const id = btnDel.getAttribute('data-id');
                const formEliminarId = 'form-eliminar'; // ID estándar del formulario eliminar
                
                // Llenamos el input oculto (Debe tener ID 'del_id')
                const inputDel = document.getElementById('del_id');
                if(inputDel) inputDel.value = id;

                mostrarSolo(formEliminarId);
            }
        });
    }

    // --- FUNCIONES AUXILIARES ---

    function rellenarFormularioEditar(data) {
        // Esta es la magia: Recorre todas las columnas que vienen de la BD
        // y busca si existe un input con ID "mod_" + nombre_columna
        Object.keys(data).forEach(key => {
            const inputId = 'mod_' + key; 
            const input = document.getElementById(inputId);
            if (input) {
                input.value = data[key];
            }
        });
        
        mostrarSolo('form-modificar');
    }

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

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Lógica para botones superiores (Agregar, Modificar, Quitar)
    const botonesAccion = document.querySelectorAll('.btn-action');
    botonesAccion.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            
            // Si ya está visible, al hacer click lo ocultamos (toggle)
            const isVisible = targetForm.style.display === 'block';
            
            // Ocultamos TODOS primero
            document.querySelectorAll('.form-section').forEach(f => f.style.display = 'none');

            // Si no estaba visible, lo mostramos
            if (!isVisible) {
                targetForm.style.display = 'block';
                targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // 2. Lógica para botones dentro de la tabla (Lápiz y Basurero)
    const tabla = document.querySelector('table');
    if (tabla) {
        tabla.addEventListener('click', (e) => {
            
            // Click en Lápiz (Editar)
            const btnEdit = e.target.closest('.btn-row-edit');
            if (btnEdit) {
                const data = JSON.parse(btnEdit.getAttribute('data-json'));
                rellenarFormularioEditar(data);
            }

            // Click en Basurero (Eliminar)
            const btnDel = e.target.closest('.btn-row-delete');
            if (btnDel) {
                const id = btnDel.getAttribute('data-id');
                // Llenamos el input oculto del formulario eliminar
                const inputDel = document.getElementById('del_id');
                if(inputDel) inputDel.value = id;
                
                mostrarSolo('form-eliminar');
            }
        });
    }

    // Funciones auxiliares
    function rellenarFormularioEditar(data) {
        // Busca inputs que coincidan con "mod_" + nombre_columna
        Object.keys(data).forEach(key => {
            const inputId = 'mod_' + key; 
            const input = document.getElementById(inputId);
            if (input) {
                input.value = data[key];
            }
        });
        mostrarSolo('form-modificar');
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

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. BOTONES DE ACCIÓN (Agregar, Modificar, Eliminar)
    const botonesAccion = document.querySelectorAll('.btn-action');
    
    botonesAccion.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Prevenir comportamiento por defecto si fuera un enlace
            e.preventDefault();

            const targetId = btn.getAttribute('data-target');
            const targetForm = document.getElementById(targetId);
            
            if (targetForm) {
                // Si ya está visible, lo ocultamos (toggle)
                const isVisible = targetForm.style.display === 'block';
                
                // Ocultamos TODOS primero
                document.querySelectorAll('.form-section').forEach(f => f.style.display = 'none');

                // Si no estaba visible, lo mostramos
                if (!isVisible) {
                    targetForm.style.display = 'block';
                    // Scroll suave hacia el formulario
                    targetForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                console.error('No se encontró el formulario con ID:', targetId);
            }
        });
    });

    // 2. BOTONES DENTRO DE LA TABLA (Lápiz y Basurero)
    const tabla = document.querySelector('table');
    if (tabla) {
        tabla.addEventListener('click', (e) => {
            
            // Click en Lápiz (Editar)
            const btnEdit = e.target.closest('.btn-row-edit');
            if (btnEdit) {
                // Evitar que recargue
                e.preventDefault(); 
                const dataString = btnEdit.getAttribute('data-json');
                console.log("Datos recibidos:", dataString); // Para depurar

                try {
                    const data = JSON.parse(dataString);
                    rellenarFormularioEditar(data);
                } catch (error) {
                    console.error("Error al leer JSON:", error);
                }
            }

            // Click en Basurero (Eliminar)
            const btnDel = e.target.closest('.btn-row-delete');
            if (btnDel) {
                e.preventDefault();
                const id = btnDel.getAttribute('data-id');
                
                // Llenamos el input oculto del formulario eliminar
                const inputDel = document.getElementById('del_id');
                if(inputDel) inputDel.value = id;
                
                mostrarSolo('form-eliminar');
            }
        });
    }

    // Funciones auxiliares
    function rellenarFormularioEditar(data) {
        // Busca inputs que coincidan con "mod_" + nombre_columna
        Object.keys(data).forEach(key => {
            const inputId = 'mod_' + key; 
            const input = document.getElementById(inputId);
            if (input) {
                input.value = data[key];
            }
        });
        mostrarSolo('form-modificar');
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