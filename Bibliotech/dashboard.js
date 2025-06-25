document.addEventListener("DOMContentLoaded", cargarUsuarios);

function cargarUsuarios() {
    fetch('get_users.php')
        .then(res => res.json())
        .then(users => {
            const tbody = document.querySelector('#userTable tbody');
            tbody.innerHTML = "";
            users.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${user.nombre}</td>
                    <td>${user.email}</td>
                    <td>${user.estado}</td>
                    <td>
                        <button onclick="eliminarUsuario(${user.id})">Eliminar</button>
                        <button onclick="cambiarContrasena(${user.id})">Cambiar Contraseña</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        });
}

function agregarUsuario() {
    const nombre = document.getElementById('nombre').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('add_user.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({nombre, email, password})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Usuario agregado');
            cargarUsuarios();
        } else {
            alert(data.error);
        }
    });
}

function eliminarUsuario(id) {
    if (!confirm("¿Estás seguro de eliminar este usuario?")) return;
    fetch('delete_user.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Usuario eliminado');
            cargarUsuarios();
        } else {
            alert(data.error);
        }
    });
}

function cambiarContrasena(id) {
    const nueva = prompt("Escribe la nueva contraseña:");
    if (!nueva) return;
    fetch('update_password.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id, password: nueva})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Contraseña actualizada');
        } else {
            alert(data.error);
        }
    });
}
