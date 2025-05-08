const imagePaths = [
    "img1.jpg",
    "img2.jpg",
    "img3.jpg",
    "img4.jpg",
    "img5.jpg",
];

const track = document.querySelector('.carousel-track');
const dotsContainer = document.querySelector('.carousel-dots');
let currentIndex = 0;

function generateCarouselCards() {
    track.innerHTML = "";
    dotsContainer.innerHTML = "";

    imagePaths.forEach((path, index) => {
        const card = document.createElement('div');
        card.classList.add('news-card');
        card.style.flex = "0 0 calc(100% / 3)";
        card.innerHTML = `
        <img src="${path}" alt="Noticia ${index + 1}" />
        <div class="card-info">
          <div class="card-meta">
            <span class="date">Fecha aleatoria</span>
            <div class="author">
              <img src="admin.jpg" alt="Admin" class="author-img" />
              <span>By Admin</span>
            </div>
          </div>
          <h3>JORNADAS DE PAZ POR EL GOBIERNO DE MÉXICO</h3>
          <p>Jalisco a la vanguardia en la promoción de la cultura de paz: es el primer Programa de su tipo en el país.</p>
          <a href="#">➞ see more</a>
        </div>
      `;
        track.appendChild(card);

        // Crear el punto (dot)
        if (index % 3 === 0) {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dotsContainer.appendChild(dot);

            // Añadir evento para mover al slide correspondiente
            dot.addEventListener('click', () => {
                moveToSlide(Math.floor(index / 3));
                resetInterval();
            });
        }
    });
}

// Actualizar el carrusel
function updateCarousel() {
    const cardWidth = track.offsetWidth / 3;
    const translateX = -(currentIndex * cardWidth * 3);
    track.style.transform = `translateX(${translateX}px)`;

    // Actualizar los puntos activos
    const dots = document.querySelectorAll('.dot');
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
    });
}

// Mover al slide específico
function moveToSlide(index) {
    currentIndex = index;
    updateCarousel();
}

// Mover al slide anterior
function prevSlide() {
    if (currentIndex > 0) {
        currentIndex--;
    } else {
        currentIndex = Math.floor((imagePaths.length - 1) / 3); // Regresar al último grupo
    }
    updateCarousel();
}

// Mover al siguiente slide
function nextSlide() {
    if (currentIndex < Math.floor((imagePaths.length - 1) / 3)) {
        currentIndex++;
    } else {
        currentIndex = 0;
    }
    updateCarousel();
}


let slideInterval = setInterval(nextSlide, 4000);


function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 4000);
}

document.querySelector('.arrow.left')?.addEventListener('click', () => {
    prevSlide();
    resetInterval();
});

document.querySelector('.arrow.right')?.addEventListener('click', () => {
    nextSlide();
    resetInterval();
});

window.addEventListener('resize', updateCarousel);

generateCarouselCards();
updateCarousel();