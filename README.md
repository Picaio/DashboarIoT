# 🌦️ ESP32 IoT Weather Dashboard (con PHP + MySQL + Chart.js)

Este proyecto te permite crear una estación meteorológica IoT completa utilizando un **ESP32** que envía datos mediante **HTTP POST** a un servidor PHP.  
Los datos se almacenan en una **base de datos MySQL** y se visualizan en un **dashboard web dinámico** con **gráficas en tiempo real usando Chart.js** y **actualización automática vía AJAX**. ⚡

---

## 🧰 Tecnologías utilizadas

- 📡 **ESP32** (conexión WiFi, envío HTTP)
- 🌐 **PHP** (backend del servidor)
- 🗃️ **MySQL** (almacenamiento de lecturas)
- 📈 **Chart.js** (visualización de datos)
- 🔄 **AJAX + jQuery** (actualización en tiempo real)
- 🎨 **HTML + CSS** (interfaz web)

---

## 📁 Estructura del repositorio

-├── HTTPS_ESP32_Cloud_Weather_Station/     📦 Código del ESP32 (Arduino IDE)
-├── SensorData_Table.sql                   ✂️ Script SQL para crear la tabla
-├── esp-database.php                       🧠 Funciones de base de datos
-├── esp-post-data.php                      📬 Recibe los datos del ESP32
-├── esp-ajax-data.php                      🔁 Endpoint JSON para gráficas
-├── esp-weather-station.php                🖥️ Dashboard principal
-├── esp-style.css                          🎨 Estilo visual del dashboard

---

## 🔧 Cómo usarlo

### 1. 🖥️ Servidor Web
- Sube los archivos `.php`, `.css` y `.sql` a tu hosting o servidor local (ej: XAMPP).
- Crea una base de datos MySQL y ejecuta `SensorData_Table.sql`.
- Configura las credenciales en `esp-database.php`.

### 2. ⚙️ ESP32
- Abre la carpeta `HTTPS_ESP32_Cloud_Weather_Station` en Arduino IDE.
- Configura tu red WiFi y URL del servidor.
- Sube el código al ESP32.

### 3. 🌐 Visualiza
- Abre en tu navegador el archivo `esp-weather-station.php`.
- Verás datos en tiempo real, gráficas y tablas actualizándose automáticamente.

---

## 🧪 Resultado esperado

- ✅ Lecturas cada 30 segundos
- ✅ Gráfico de temperatura y humedad
- ✅ Gauges actualizados dinámicamente
- ✅ Tabla de últimos registros
- ✅ Todo funcionando sin Firebase 😎

---

## 📜 Licencia

Este proyecto se encuentra bajo la licencia MIT.  
¡Si lo usas, menciónalo o compártelo con otros makers! 🤝

---

## ✨ Autor

Creado por [@PICAIO](https://github.com/picaio)  
Para más proyectos, tutoriales y contenido sobre electrónica e IoT, visita mi canal o perfil.

