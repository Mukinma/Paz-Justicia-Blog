const teamMembers = [
    {
        name: "Ballinas",
        role: "Coordinadora General",
        description: "Activista gay y periodista con más de 10 años trabajando por los derechos humanos.",
        img: "prueba1.jpg"
    },
    {
        name: "Arthur",
        role: "Diseñador UX",
        description: "Apasionado por el diseño inclusivo y accesible.",
        img: "prueba2.jpg"
    },
    {
        name: "Juan Master",
        role: "Editora de contenidos",
        description: "Comprometida con la verdad y la justicia social.",
        img: "prueba3.jpg"
    },
    {
        name: "Gonzalo Rip",
        role: "Desarrollador Web",
        description: "Crea herramientas digitales para el cambio social.",
        img: "prueba4.jpg"
    },
    {
        name: "Navarrito",
        role: "Community Manager",
        description: "Conecta con las personas en redes y comunidades locales.",
        img: "prueba5.jpg"
    }
];

const track = document.querySelector('.carousel-track');
const dotsContainer = document.querySelector('.carousel-dots');
const leftBtn = document.querySelector('.arrow.left');
const rightBtn = document.querySelector('.arrow.right');

let currentIndex = 0;
const cardsToShow = 3;

//tarjetas dinámicamente
teamMembers.forEach(member => {
    const card = document.createElement('div');
    card.className = 'team-card';
    card.innerHTML = `
      <img src="${member.img}" alt="${member.name}" />
      <div class="card-info">
        <h3>${member.name}</h3>
        <p class="role">${member.role}</p>
        <p>${member.description}</p>
      </div>
    `;
    track.appendChild(card);
});

// ancho de la tarjeta
const cards = document.querySelectorAll('.team-card');
const cardWidth = cards[0].offsetWidth + 20;
const maxIndex = teamMembers.length - cardsToShow;

// puntos de navegación
for (let i = 0; i <= maxIndex; i++) {
    const dot = document.createElement('span');
    dot.classList.add('dot');
    if (i === 0) dot.classList.add('active');
    dot.addEventListener('click', () => {
        currentIndex = i;
        updateCarousel();
    });
    dotsContainer.appendChild(dot);
}

// Mover carrusel
function updateCarousel() {
    track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
    document.querySelectorAll('.dot').forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
    });
}

// Flechas
leftBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        updateCarousel();
    }
});

rightBtn.addEventListener('click', () => {
    if (currentIndex < maxIndex) {
        currentIndex++;
        updateCarousel();
    }
});
