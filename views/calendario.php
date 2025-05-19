<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calendario de Marchas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../views/css/style.css">
    <link rel="stylesheet" href="../views/css/footer.css">
    <link rel="stylesheet" href="../views/css/calendario.css">
</head>

<body>
    <?php include_once '../views/includes/header.php'; ?>
    
    <div class="main-content">
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

        <div id="eventPanel" class="event-panel">
            <h3 id="panelTitle"></h3>
            <p id="panelDetails"></p>
            <img id="panelImage" src="" alt="">
        </div>
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
                            'Marcha Día de la Mujer': '../assets/involucrate/marcha.jpg',
                            'Marcha por el Clima': '../assets/involucrate/marcha.jpg',
                            'Marcha del Orgullo': '../assets/involucrate/marcha.jpg',
                            'Marcha por la Paz Mundial': '../assets/involucrate/marcha.jpg',
                            'Marcha por los Derechos Humanos': '../assets/involucrate/marcha.jpg'
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

    <?php include_once '../views/includes/footer.php'; ?>
</body>

</html>