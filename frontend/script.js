document.addEventListener("DOMContentLoaded", function () {
  const showInfo = document.querySelector('.message-container'); // Contenedor para mostrar mensajes
  const categoryButtons = document.querySelectorAll('.class_btn'); // Botones de categorías
  const actionButtons = document.querySelectorAll('.action-btn'); // Botones de acciones

  // Base URLs para las APIs disponibles
  const baseURLs = {
    class_storage: "http://127.0.0.1:8000/api/hello",
    json: "http://127.0.0.1:8000/api/json",
    csv: "http://127.0.0.1:8000/api/csv",
    xml: "http://127.0.0.1:8000/api/xml"
  };

  let selectedCategory = "class_storage"; // Categoría seleccionada por defecto

  // Cambiar entre Class Storage, JSON, CSV, XML
  categoryButtons.forEach(button => {
    button.addEventListener('click', function () {
      // Eliminar selección previa
      categoryButtons.forEach(btn => btn.classList.remove('active'));

      // Marcar el botón actual como activo
      this.classList.add('active');

      // Actualizar categoría seleccionada
      selectedCategory = this.textContent.toLowerCase().replace(" ", "_");
      showInfo.innerHTML = `Selected: ${this.textContent}`; // Mostrar categoría seleccionada
    });
  });

  // Función para realizar solicitudes HTTP
  const fetchData = async (method, endpoint = "", body = null) => {
    try {
      const url = `${baseURLs[selectedCategory]}${endpoint}`;
      const options = {
        method,
        headers: { "Content-Type": "application/json" },
      };
      if (body) options.body = JSON.stringify(body);
      const response = await fetch(url, options);
      if (!response.ok) {
        // Mostrar error específico
        throw new Error(
          `Error ${response.status}: ${response.statusText || "Unknown Error"}`
        );
      }
      const data = await response.json();
      showInfo.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`; // Mostrar la respuesta JSON de forma legible
    } catch (error) {
      showInfo.innerHTML = `<p>Error: ${error.message}</p>`;
    }
  };

  // Operaciones CRUD
  document.querySelector("#get-files").addEventListener("click", () => {
    fetchData("GET"); // Obtener archivos de la categoría seleccionada
  });

  document.querySelector("#store").addEventListener("click", () => {
    const filename = prompt(
      `Enter filename (e.g., file.${selectedCategory === "csv" ? "csv" : "txt"}):`
    );
    const content =
      selectedCategory === "csv"
        ? prompt(
            "Enter file content in CSV format (e.g., header1,header2\\nvalue1,value2):"
          )
        : prompt("Enter file content:");
    if (filename && content) {
      if (selectedCategory === "csv" && !content.includes(",")) {
        showInfo.innerHTML = "Invalid CSV format! Ensure it includes commas.";
        return;
      }
      const requestBody = { filename, content };
      fetchData("POST", "", requestBody); // Enviar archivo para almacenar
    } else {
      showInfo.innerHTML = "Filename or content cannot be empty!";
    }
  });

  document.querySelector("#show").addEventListener("click", () => {
    const filename = prompt("Enter filename to show:");
    if (filename) {
      fetchData("GET", `/${filename}`); // Mostrar contenido del archivo
    } else {
      showInfo.innerHTML = "Filename cannot be empty!";
    }
  });

  document.querySelector("#update").addEventListener("click", () => {
    const filename = prompt("Enter filename to update:");
    const content =
      selectedCategory === "csv"
        ? prompt(
            "Enter new content in CSV format (e.g., header1,header2\\nvalue1,value2):"
          )
        : prompt("Enter new content:");
    if (filename && content) {
      if (selectedCategory === "csv" && !content.includes(",")) {
        showInfo.innerHTML = "Invalid CSV format! Ensure it includes commas.";
        return;
      }
      const requestBody = { content };
      fetchData("PUT", `/${filename}`, requestBody); // Actualizar el archivo
    } else {
      showInfo.innerHTML = "Filename or content cannot be empty!";
    }
  });

  document.querySelector("#delete").addEventListener("click", () => {
    const filename = prompt("Enter filename to delete:");
    if (filename) {
      fetchData("DELETE", `/${filename}`); // Eliminar el archivo
    } else {
      showInfo.innerHTML = "Filename cannot be empty!";
    }
  });

});
