const teamMembers = [
    {
        name: "Christopher Eugenio Nieves Martínez",
        role: "Líder del Equipo y Programador Full Stack",
        description: "Coordinador del proyecto, experto en desarrollo web integral, manejo de bases de datos, seguridad y arquitectura de sistemas.",
        img: "../assets/team/member1.jpg",
        social: [
            { platform: "linkedin", url: "#" },
            { platform: "github", url: "#" }
        ]
    },
    {
        name: "Fatima Isabel Contreras Avalos",
        role: "Diseñadora UI/UX y Programadora Frontend",
        description: "Especialista en experiencia de usuario, creando interfaces atractivas e intuitivas que conectan con nuestros visitantes.",
        img: "../assets/team/member2.jpg",
        social: [
            { platform: "linkedin", url: "#" },
            { platform: "instagram", url: "#" }
        ]
    },
    {
        name: "Karol Magdalena Sinsel Torres",
        role: "Programadora Frontend Principal",
        description: "Experta en desarrollo de interfaces responsivas y accesibles, enfocada en la optimización y rendimiento del sitio.",
        img: "../assets/team/member3.jpg",
        social: [
            { platform: "linkedin", url: "#" },
            { platform: "github", url: "#" }
        ]
    },
    {
        name: "Dimas Rolon Aram Sebastian",
        role: "Programador Backend",
        description: "Desarrollador de la estructura del servidor, bases de datos y seguridad de la plataforma, garantizando un funcionamiento óptimo.",
        img: "../assets/team/member4.jpg",
        social: [
            { platform: "linkedin", url: "#" },
            { platform: "github", url: "#" }
        ]
    }
];

document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const dotsContainer = document.querySelector('.carousel-dots');
    const leftBtn = document.querySelector('.arrow.left');
    const rightBtn = document.querySelector('.arrow.right');
    
    // Añadir clases animate inmediatamente para evitar que el contenido desaparezca
    const aboutText = document.querySelector('.about-text');
    const aboutImages = document.querySelector('.about-images');
    const carousel = document.querySelector('.news-carousel');
    
    // Aplicar animate a los elementos con un ligero retraso para asegurar que la transición sea visible
    setTimeout(() => {
        if (aboutText) aboutText.classList.add('animate');
        if (aboutImages) aboutImages.classList.add('animate');
        if (carousel) carousel.classList.add('animate');
    }, 100);

    let currentIndex = 0;
    let cardsToShow = 3;
    
    // Ajustar número de tarjetas según el ancho de pantalla
    function updateCardsToShow() {
        if (window.innerWidth < 768) {
            cardsToShow = 1;
        } else if (window.innerWidth < 992) {
            cardsToShow = 2;
        } else {
            cardsToShow = 3;
        }
        
        const maxIndex = Math.max(0, teamMembers.length - cardsToShow);
        if (currentIndex > maxIndex) currentIndex = maxIndex;
        updateCarousel();
        updateDots();
    }
    
    // Crear tarjetas dinámicamente
    teamMembers.forEach(member => {
        const card = document.createElement('div');
        card.className = 'carousel-item';
        
        let socialHTML = '';
        if (member.social && member.social.length > 0) {
            socialHTML = '<div class="social-links">';
            member.social.forEach(social => {
                let icon = '';
                switch(social.platform) {
                    case 'twitter':
                        icon = 'fa-twitter';
                        break;
                    case 'facebook':
                        icon = 'fa-facebook-f';
                        break;
                    case 'instagram':
                        icon = 'fa-instagram';
                        break;
                    case 'linkedin':
                        icon = 'fa-linkedin-in';
                        break;
                    case 'github':
                        icon = 'fa-github';
                        break;
                    default:
                        icon = 'fa-link';
                }
                socialHTML += `<a href="${social.url}" target="_blank"><i class="fab ${icon}"></i></a>`;
            });
            socialHTML += '</div>';
        }
        
        card.innerHTML = `
            <img src="${member.img}" alt="${member.name}" onerror="this.src='../assets/user.svg'; this.onerror='';" />
            <div class="carousel-item-content">
                <h3>${member.name}</h3>
                <span class="role">${member.role}</span>
                <p>${member.description}</p>
                ${socialHTML}
            </div>
        `;
        track.appendChild(card);
    });

    // Crear puntos de navegación
    function updateDots() {
        dotsContainer.innerHTML = '';
        const maxIndex = Math.max(0, teamMembers.length - cardsToShow);
        
        for (let i = 0; i <= maxIndex; i++) {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (i === currentIndex) dot.classList.add('active');
            dot.addEventListener('click', () => {
                currentIndex = i;
                updateCarousel();
            });
            dotsContainer.appendChild(dot);
        }
    }

    // Calcular ancho de tarjeta
    function getCardWidth() {
        const cards = document.querySelectorAll('.carousel-item');
        if (cards.length === 0) return 0;
        
        const cardStyle = window.getComputedStyle(cards[0]);
        const cardWidth = cards[0].offsetWidth;
        const marginRight = parseInt(cardStyle.marginRight) || 0;
        const marginLeft = parseInt(cardStyle.marginLeft) || 0;
        
        return cardWidth + marginRight + marginLeft;
    }

    // Mover carrusel
    function updateCarousel() {
        const cardWidth = getCardWidth();
        track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        
        document.querySelectorAll('.dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
        
        // Actualizar estados de los botones
        const maxIndex = Math.max(0, teamMembers.length - cardsToShow);
        leftBtn.classList.toggle('disabled', currentIndex === 0);
        rightBtn.classList.toggle('disabled', currentIndex === maxIndex);
    }

    // Botones de navegación
    leftBtn.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    rightBtn.addEventListener('click', () => {
        const maxIndex = Math.max(0, teamMembers.length - cardsToShow);
        if (currentIndex < maxIndex) {
            currentIndex++;
            updateCarousel();
        }
    });

    // Responsive
    window.addEventListener('resize', () => {
        updateCardsToShow();
    });

    // Inicializar
    updateCardsToShow();
});

// Seguimos manteniendo la animación al hacer scroll como respaldo
window.addEventListener('scroll', () => {
    const aboutText = document.querySelector('.about-text');
    const aboutImages = document.querySelector('.about-images');
    const carousel = document.querySelector('.news-carousel');
    
    if (isElementInViewport(aboutText) && !aboutText.classList.contains('animate')) {
        aboutText.classList.add('animate');
    }
    
    if (isElementInViewport(aboutImages) && !aboutImages.classList.contains('animate')) {
        aboutImages.classList.add('animate');
    }
    
    if (isElementInViewport(carousel) && !carousel.classList.contains('animate')) {
        carousel.classList.add('animate');
    }
});

function isElementInViewport(el) {
    if (!el) return false;
    
    const rect = el.getBoundingClientRect();
    return (
        rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 &&
        rect.bottom >= 0
    );
}
