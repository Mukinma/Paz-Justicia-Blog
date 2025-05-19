<!DOCTYPE html>
<html lang="es">
    <link rel="icon" href="assets/minilogo.png">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calendario de Marchas</title>
    <style>
        header {
            width: 100%;
            height: 60px;
            padding: 40px;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
        }

        header .logo {
            height: 40px;
            cursor: pointer;
        }

        .search-bar {
            position: relative;
            flex-grow: 1;
            max-width: 500px;
            margin-left: 30px;
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .search-bar input {
            width: 100%;
            color: #eee;
            padding: 6px 36px 6px 12px;
            border: none;
            outline: none;
            font-size: 14px;
            background-color: transparent;
            font-weight: 400;
        }

        .search-bar .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 16px;
            color: #555;
        }


        header nav {
            display: flex;
            gap: 30px;
        }

        header nav a {
            color: #eee;
            text-decoration: none;
            font-weight: 500;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #5993c0, #35688e);
            color: #333;
            text-align: center;
            padding: 80px;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            gap: 40px;
        }

        h1 {
            color: #ffffff;
            margin-bottom: 30px;
            font-size: 2.5em;
        }

        .calendar-container {
            position: relative;
            margin: 40px 0 0 35px;
            max-width: 450px;
            background: rgba(182, 217, 248, 0.676);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .controls {
            margin-bottom: 20px;
            color: #34495E;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        select,
        button {
            background-color: #3498db;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 14px;
            color: #fff;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.2s;
            margin: 0 5px;
        }

        select:hover,
        button:hover {
            background-color: #2980b9;
            transform: scale(1.05);
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            font-size: 1.2em;
        }

        .calendar .header {
            padding: 10px;
            border-radius: 10px;
            background: #2C3E50;
            color: #fff;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .calendar .day {
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
        }

        .calendar .day:hover {
            background: #f39c12;
            color: #fff;
            transform: scale(1.1);
        }

        .calendar .day.has-event {
            background: #f1c40f;
            font-weight: bold;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 400px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(128, 176, 239, 0.612);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 25px 30px;
            width: 80%;
            max-width: 800px;
            margin: auto;
            color: #fff;
            position: relative;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <header>
        <img src="image/logo.png" class="logo" onclick="location.href='index.html'">

        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <span class="search-icon">🔍</span>
        </div>

        <nav>
            <a href="">Home</a>
            <a href="contact/contact.html">Contact</a>
            <a href="">Info</a>
            <a href="login/login.html" class="btn">Login</a>
        </nav>
    </header>

    <div class="calendar-container">
        <h1>Calendario de Marchas</h1>
        <div class="controls">
            <button onclick="changeMonth(-1)">⬅️ Mes Anterior</button>
            <select id="monthSelect" onchange="changeMonthSelect()"></select>
            <select id="yearSelect" onchange="changeYear()"></select>
            <button onclick="changeMonth(1)">Mes Siguiente ➡️</button>
        </div>
        <div class="calendar" id="calendar"></div>
    </div>

    <div id="eventPanel"
        style="margin-left: 40px; max-width: 600px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); padding: 20px; border-radius: 15px; display: none; color: white; position: relative; margin: 40px 0 0 35px;">
        <h3 id="panelTitle" style="margin-top: 0;"></h3>
        <p id="panelDetails"></p>
        <img id="panelImage" src="" alt="" style="width:100%; border-radius: 10px; margin-top: 10px; display: none;" />
    </div>

    <div id="eventModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 id="modalTitle"></h3>
            <p id="modalDetails"></p>
        </div>
    </div>

    <script>
        const marchas = [
            { fecha: '2025-03-08', titulo: 'Marcha Día de la Mujer', lugar: 'CDMX' },
            { fecha: '2025-04-22', titulo: 'Marcha por el Clima', lugar: 'Bogotá' },
            { fecha: '2025-06-28', titulo: 'Marcha del Orgullo', lugar: 'Madrid' },
            { fecha: '2025-09-21', titulo: 'Marcha por la Paz Mundial', lugar: 'Buenos Aires' },
            { fecha: '2025-12-10', titulo: 'Marcha por los Derechos Humanos', lugar: 'Santiago' }
        ];

        const months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        function createCalendar(month, year) {
            const calendar = document.getElementById('calendar');
            calendar.innerHTML = '';
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            dayNames.forEach(day => {
                const div = document.createElement('div');
                div.className = 'header';
                div.textContent = day;
                calendar.appendChild(div);
            });

            for (let i = 0; i < firstDay; i++) {
                calendar.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const event = marchas.find(e => e.fecha === dateStr);
                const div = document.createElement('div');
                div.className = 'day';
                if (event) {
                    div.classList.add('has-event');
                    div.onclick = () => {
                        document.getElementById('panelTitle').textContent = event.titulo;
                        document.getElementById('panelDetails').textContent = `${event.lugar}, ${event.fecha}`;

                        // Si quieres una imagen asociada al evento, puedes hacer algo como esto:
                        const imageMap = {
                            'Marcha Día de la Mujer': 'marcha.jpg',
                            'Marcha por el Clima': 'marcha.jpg',
                            'Marcha del Orgullo': 'marcha.jpg',
                            'Marcha por la Paz Mundial': 'marcha.jpg',
                            'Marcha por los Derechos Humanos': 'img/ddhh.jpg'
                        };
                        const imgSrc = imageMap[event.titulo];
                        const img = document.getElementById('panelImage');

                        if (imgSrc) {
                            img.src = imgSrc;
                            img.style.display = 'block';
                        } else {
                            img.style.display = 'none';
                        }

                        document.getElementById('eventPanel').style.display = 'block';
                    };

                }
                div.textContent = day;
                calendar.appendChild(div);
            }
        }

        function changeMonth(increment) {
            currentMonth += increment;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            } else if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            updateCalendar();
        }

        function changeMonthSelect() {
            const selectedMonth = document.getElementById('monthSelect').value;
            currentMonth = parseInt(selectedMonth, 10);
            updateCalendar();
        }

        function changeYear() {
            const selectedYear = document.getElementById('yearSelect').value;
            currentYear = parseInt(selectedYear, 10);
            updateCalendar();
        }

        function updateCalendar() {
            createCalendar(currentMonth, currentYear);
            document.getElementById('monthSelect').value = currentMonth;
            document.getElementById('yearSelect').value = currentYear;
        }


        document.getElementById('monthSelect').innerHTML = months
            .map((m, i) => `<option value="${i}">${m}</option>`)
            .join('');

        for (let year = 2025; year <= 2030; year++) {
            const opt = document.createElement('option');
            opt.value = year;
            opt.textContent = year;
            document.getElementById('yearSelect').appendChild(opt);
        }

        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }

        // Inicializa el calendario con el mes y año actual
        updateCalendar();

    </script>
</body>

</html>