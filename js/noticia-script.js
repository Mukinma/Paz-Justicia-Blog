/**
 * Controlador del Carousel de Tendencias
 * Desarrollado para Peace in Progress
 * Versión: 1.0
 */
class TrendingCarousel {
    constructor() {
        // Referencias a elementos del DOM
        this.carousel = document.querySelector('.carousel');
        this.list = this.carousel?.querySelector('.list');
        this.slides = this.list?.querySelectorAll('.item');
        this.thumbnails = document.querySelectorAll('.carousel .thumbnail .item');
        this.prevBtn = document.getElementById('prev');
        this.nextBtn = document.getElementById('next');
        this.timeBar = this.carousel?.querySelector('.time');
        
        // Verificar si hay carousel en la página
        if (!this.carousel || !this.list || !this.slides || this.slides.length === 0) {
            console.warn('Carousel no encontrado o sin slides disponibles');
            return;
        }
        
        // Estado interno optimizado
        this.state = {
            currentIndex: this.findInitialActiveIndex(),
            previousIndex: null,
            totalSlides: this.slides.length,
            isAnimating: false,
            autoplayEnabled: true,
            userInteracted: false,
            lastTransitionTime: 0,
            touchStartX: 0,
            isTouching: false
        };
        
        // Configuración
        this.config = {
            autoplayDelay: 7000,           // 7 segundos
            animationDuration: 800,         // 0.8 segundos
            contentDelay: 150,              // 0.15 segundos
            swipeThreshold: 50,             // píxeles para detectar swipe
            debounceDelay: 200              // delay para debounce de eventos
        };
        
        // Control de timers
        this.timers = {
            autoplay: null,
            animation: null,
            debounce: null
        };
        
        // Inicializar
        this.init();
    }
    
    /**
     * Encuentra el índice del slide activo inicial
     */
    findInitialActiveIndex() {
        // Buscar el elemento con clase 'active'
        for (let i = 0; i < this.slides.length; i++) {
            if (this.slides[i].classList.contains('active')) {
                return i;
            }
        }
        
        // Si no hay ninguno activo, activar el primero
        if (this.slides.length > 0) {
            this.slides[0].classList.add('active');
            return 0;
        }
        
        return -1;
    }
    
    /**
     * Inicializa el carousel
     */
    init() {
        console.log(`Inicializando carousel con ${this.state.totalSlides} slides`);
        
        // Preparar el slide inicial
        this.preloadCriticalImages();
        this.applyInitialStyles();
        
        // Configurar eventos
        this.setupEventListeners();
        
        // Iniciar autoplay después de que todo esté listo
        this.state.lastTransitionTime = Date.now();
        setTimeout(() => this.startAutoplay(), 1000);
        
        // Aplicar clase de inicialización
        this.carousel.classList.add('carousel-initialized');
    }
    
    /**
     * Precarga imágenes críticas para mejorar la experiencia
     */
    preloadCriticalImages() {
        // Precargar imágenes del slide actual y adyacentes
        const slidesToPreload = [
            this.slides[this.state.currentIndex],
            this.slides[(this.state.currentIndex + 1) % this.state.totalSlides],
            this.slides[(this.state.currentIndex - 1 + this.state.totalSlides) % this.state.totalSlides]
        ];
        
        slidesToPreload.forEach(slide => {
            if (!slide) return;
            
            const img = slide.querySelector('img');
            if (img) {
                img.loading = 'eager';
                const preloadImg = new Image();
                preloadImg.src = img.src;
            }
        });
    }
    
    /**
     * Aplica estilos iniciales para el slide activo
     */
    applyInitialStyles() {
        // Resetear todos los slides
        this.slides.forEach(slide => {
            slide.classList.remove('active');
            slide.style.opacity = '0';
            slide.style.visibility = 'hidden';
            slide.style.zIndex = '0';
            
            const content = slide.querySelector('.content');
            if (content) {
                content.style.opacity = '0';
                content.style.transform = 'translateY(-50%) translateX(-30px)';
            }
        });
        
        // Configurar slide activo
        const activeSlide = this.slides[this.state.currentIndex];
        if (activeSlide) {
            activeSlide.classList.add('active');
            activeSlide.style.opacity = '1';
            activeSlide.style.visibility = 'visible';
            activeSlide.style.zIndex = '5';
            
            const content = activeSlide.querySelector('.content');
            if (content) {
                content.style.opacity = '1';
                content.style.transform = 'translateY(-50%) translateX(0)';
                
                // Inicializar métricas visibles
                const metrics = content.querySelector('.metrics');
                if (metrics) {
                    metrics.style.opacity = '1';
                    metrics.style.transform = 'translateY(0)';
                }
            }
        }
        
        // Actualizar miniaturas
        this.updateThumbnails();
    }
    
    /**
     * Configura todos los event listeners
     */
    setupEventListeners() {
        // Botones de navegación
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', this.onPrevButtonClick.bind(this));
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', this.onNextButtonClick.bind(this));
        }
        
        // Click en miniaturas (thumbnails)
        this.thumbnails.forEach((thumb, idx) => {
            thumb.addEventListener('click', () => this.onThumbnailClick(idx));
        });
        
        // Eventos de mouse para pausar/reanudar autoplay
        this.carousel.addEventListener('mouseenter', this.onCarouselMouseEnter.bind(this));
        this.carousel.addEventListener('mouseleave', this.onCarouselMouseLeave.bind(this));
        
        // Eventos táctiles
        this.carousel.addEventListener('touchstart', this.onTouchStart.bind(this), { passive: true });
        this.carousel.addEventListener('touchmove', this.onTouchMove.bind(this), { passive: true });
        this.carousel.addEventListener('touchend', this.onTouchEnd.bind(this), { passive: true });
        
        // Navegación con teclado
        document.addEventListener('keydown', this.onKeyDown.bind(this));
        
        // Visibilidad de la página
        document.addEventListener('visibilitychange', this.onVisibilityChange.bind(this));
        
        // Evento de resize para ajustes responsivos
        window.addEventListener('resize', this.debounce(this.onWindowResize.bind(this), this.config.debounceDelay));
    }
    
    /**
     * Handler para click en botón previo
     */
    onPrevButtonClick(e) {
        e.preventDefault();
        this.state.userInteracted = true;
        if (!this.state.isAnimating) {
            this.showPrevSlide();
            this.resetAutoplay();
        }
    }
    
    /**
     * Handler para click en botón siguiente
     */
    onNextButtonClick(e) {
        e.preventDefault();
        this.state.userInteracted = true;
        if (!this.state.isAnimating) {
            this.showNextSlide();
            this.resetAutoplay();
        }
    }
    
    /**
     * Handler para click en miniaturas
     */
    onThumbnailClick(thumbIndex) {
        this.state.userInteracted = true;
        
        // Calcular el índice del slide correspondiente a la miniatura
        const targetIndex = this.getThumbnailTargetIndex(thumbIndex);
        
        if (!this.state.isAnimating && 
            targetIndex !== this.state.currentIndex && 
            targetIndex >= 0 && 
            targetIndex < this.state.totalSlides) {
            
            this.showSlide(targetIndex);
            this.resetAutoplay();
        }
    }
    
    /**
     * Calcular el índice del slide correspondiente a una miniatura
     */
    getThumbnailTargetIndex(thumbIndex) {
        // Si hay igual número de slides y miniaturas, mapeo directo
        if (this.thumbnails.length === this.state.totalSlides) {
            return thumbIndex;
        }
        
        // Si hay menos miniaturas que slides, lógica avanzada de mapeo
        if (thumbIndex < this.state.currentIndex) {
            return thumbIndex;
        } else {
            return thumbIndex + 1;
        }
    }
    
    /**
     * Handler para interacción táctil - inicio
     */
    onTouchStart(e) {
        this.state.userInteracted = true;
        this.state.touchStartX = e.changedTouches[0].screenX;
        this.state.isTouching = true;
        this.stopAutoplay();
    }
    
    /**
     * Handler para interacción táctil - movimiento
     */
    onTouchMove(e) {
        if (!this.state.isTouching) return;
        
        // Posibilidad de añadir aquí seguimiento visual del deslizamiento
    }
    
    /**
     * Handler para interacción táctil - fin
     */
    onTouchEnd(e) {
        if (!this.state.isTouching) return;
        
        this.state.isTouching = false;
        
        if (this.state.isAnimating) return;
        
        const endX = e.changedTouches[0].screenX;
        const diffX = this.state.touchStartX - endX;
        
        // Si el desplazamiento excede el umbral, cambiar slide
        if (Math.abs(diffX) > this.config.swipeThreshold) {
            if (diffX > 0) {
                this.showNextSlide();
            } else {
                this.showPrevSlide();
            }
        }
        
        // Reiniciar autoplay después de un momento
        this.resetAutoplay();
    }
    
    /**
     * Handler para entrada del mouse en el carousel
     */
    onCarouselMouseEnter() {
        this.state.userInteracted = true;
        this.stopAutoplay();
    }
    
    /**
     * Handler para salida del mouse del carousel
     */
    onCarouselMouseLeave() {
        this.state.userInteracted = false;
        this.startAutoplay();
    }
    
    /**
     * Handler para eventos de teclado
     */
    onKeyDown(e) {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            this.state.userInteracted = true;
            
            if (!this.state.isAnimating) {
                if (e.key === 'ArrowLeft') {
                    this.showPrevSlide();
                } else {
                    this.showNextSlide();
                }
                
                this.resetAutoplay();
            }
        }
    }
    
    /**
     * Handler para cambios de visibilidad de la página
     */
    onVisibilityChange() {
        if (document.hidden) {
            this.stopAutoplay();
        } else if (!this.state.userInteracted) {
            this.startAutoplay();
        }
    }
    
    /**
     * Handler para eventos de redimensionamiento de ventana
     */
    onWindowResize() {
        // Podríamos ajustar aspectos visuales en función del tamaño
        this.updateThumbnails();
    }
    
    /**
     * Muestra un slide específico
     */
    showSlide(index) {
        // Validar índice
        if (index < 0 || index >= this.state.totalSlides) {
            console.error(`Índice de slide inválido: ${index}`);
            return;
        }
        
        // No hacer nada si ya estamos en ese slide
        if (index === this.state.currentIndex || this.state.isAnimating) {
            return;
        }
        
        // Activar flag de animación
        this.state.isAnimating = true;
        
        // Guardar índice anterior y actualizar actual
        this.state.previousIndex = this.state.currentIndex;
        this.state.currentIndex = index;
        this.state.lastTransitionTime = Date.now();
        
        // Obtener referencias a los slides
        const currentSlide = this.slides[this.state.previousIndex];
        const nextSlide = this.slides[index];
        
        if (!nextSlide) {
            console.error('Slide destino no encontrado');
            this.state.isAnimating = false;
            return;
        }
        
        // Limpiar cualquier animación previa
        this.clearTimers();
        
        // Ejecutar la transición
        this.executeTransition(currentSlide, nextSlide);
    }
    
    /**
     * Ejecuta la transición entre slides
     */
    executeTransition(currentSlide, nextSlide) {
        // Fase 1: Preparación - configurar todos los slides
        this.slides.forEach(slide => {
            if (slide === nextSlide) {
                slide.style.zIndex = '10';  // Slide entrante al frente
            } else if (slide === currentSlide) {
                slide.style.zIndex = '5';   // Slide saliente detrás
            } else {
                slide.style.zIndex = '0';   // Otros slides al fondo
            }
        });
        
        // Fase 2: Configurar slide entrante
        nextSlide.style.visibility = 'visible';
        nextSlide.style.opacity = '0';
        nextSlide.classList.add('active');
        
        // Fase 3: Animar la transición usando requestAnimationFrame para mejor rendimiento
        requestAnimationFrame(() => {
            // Animar salida del slide actual
            if (currentSlide) {
                currentSlide.style.transition = `opacity ${this.config.animationDuration/2}ms ease-out`;
                currentSlide.style.opacity = '0';
            }
            
            // Animar entrada del nuevo slide
            nextSlide.style.transition = `opacity ${this.config.animationDuration}ms ease-in`;
            
            // Ejecutar en el siguiente frame para asegurar transición fluida
            requestAnimationFrame(() => {
                nextSlide.style.opacity = '1';
                
                // Animar contenido del nuevo slide
                const nextContent = nextSlide.querySelector('.content');
                if (nextContent) {
                    nextContent.style.opacity = '0';
                    nextContent.style.transform = 'translateY(-50%) translateX(-30px)';
                    
                    setTimeout(() => {
                        nextContent.style.opacity = '1';
                        nextContent.style.transform = 'translateY(-50%) translateX(0)';
                        
                        // Animar entrada de las métricas con un pequeño retraso adicional
                        const metrics = nextContent.querySelector('.metrics');
                        if (metrics) {
                            metrics.style.transform = 'translateY(20px)';
                            metrics.style.opacity = '0';
                            
                            setTimeout(() => {
                                metrics.style.transition = 'all 0.4s ease-out';
                                metrics.style.transform = 'translateY(0)';
                                metrics.style.opacity = '1';
                            }, 200);
                        }
                    }, this.config.contentDelay);
                }
                
                // Finalizar animación del slide anterior
                if (currentSlide) {
                    setTimeout(() => {
                        currentSlide.classList.remove('active');
                        currentSlide.style.visibility = 'hidden';
                        
                        const currentContent = currentSlide.querySelector('.content');
                        if (currentContent) {
                            currentContent.style.opacity = '0';
                        }
                    }, this.config.animationDuration/2);
                }
            });
            
            // Actualizar miniaturas
            this.updateThumbnails();
            
            // Añadir clase de transición
            this.carousel.classList.add('transitioning');
            
            // Programar finalización de la transición
            this.timers.animation = setTimeout(() => {
                this.finalizeTransition();
            }, this.config.animationDuration + 50);
        });
    }
    
    /**
     * Finaliza la transición y limpia
     */
    finalizeTransition() {
        // Quitar clase de transición
        this.carousel.classList.remove('transitioning');
        
        // Verificar y asegurar estados correctos
        this.slides.forEach((slide, i) => {
            // Limpiar transiciones
            slide.style.transition = '';
            
            // Configurar visibilidad según el estado
            if (i === this.state.currentIndex) {
                slide.style.visibility = 'visible';
                slide.style.opacity = '1';
                slide.style.zIndex = '5';
                slide.classList.add('active');
                
                const content = slide.querySelector('.content');
                if (content) {
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(-50%) translateX(0)';
                    
                    // Asegurar que las métricas estén visibles
                    const metrics = content.querySelector('.metrics');
                    if (metrics) {
                        metrics.style.transition = '';
                        metrics.style.opacity = '1';
                        metrics.style.transform = 'translateY(0)';
                    }
                }
            } else {
                slide.style.visibility = 'hidden';
                slide.style.opacity = '0';
                slide.style.zIndex = '0';
                slide.classList.remove('active');
                
                const content = slide.querySelector('.content');
                if (content) {
                    content.style.opacity = '0';
                    
                    // Ocultar métricas
                    const metrics = content.querySelector('.metrics');
                    if (metrics) {
                        metrics.style.opacity = '0';
                    }
                }
            }
        });
        
        // Asegurar que los thumbnails están sincronizados
        this.updateThumbnails();
        
        // Desbloquear animación
        this.state.isAnimating = false;
    }
    
    /**
     * Actualiza los thumbnails según el slide activo
     */
    updateThumbnails() {
        if (!this.thumbnails || this.thumbnails.length === 0) return;
        
        // Si hay igual número de slides y thumbnails
        if (this.thumbnails.length === this.state.totalSlides) {
            this.thumbnails.forEach((thumb, idx) => {
                thumb.classList.toggle('active', idx === this.state.currentIndex);
                thumb.style.display = 'block';
            });
            return;
        }
        
        // Si hay diferentes cantidades (caso típico: menos thumbnails que slides)
        // Determinar qué thumbnail debe ocultarse (el correspondiente al slide activo)
        let thumbnailToHide = -1;
        
        if (this.state.currentIndex === 0) {
            thumbnailToHide = 0;
        } else {
            thumbnailToHide = this.state.currentIndex - 1;
            
            // Comprobar que no intentamos ocultar un thumbnail que no existe
            if (thumbnailToHide >= this.thumbnails.length) {
                thumbnailToHide = this.thumbnails.length - 1;
            }
        }
        
        // Aplicar visibilidad a los thumbnails
        this.thumbnails.forEach((thumb, idx) => {
            thumb.classList.remove('active');
            
            if (idx === thumbnailToHide && thumbnailToHide >= 0) {
                thumb.style.display = 'none';
            } else {
                thumb.style.display = 'block';
            }
        });
    }
    
    /**
     * Muestra el slide anterior
     */
    showPrevSlide() {
        if (this.state.totalSlides <= 1) return;
        
        const prevIndex = (this.state.currentIndex - 1 + this.state.totalSlides) % this.state.totalSlides;
        this.showSlide(prevIndex);
    }
    
    /**
     * Muestra el slide siguiente
     */
    showNextSlide() {
        if (this.state.totalSlides <= 1) return;
        
        const nextIndex = (this.state.currentIndex + 1) % this.state.totalSlides;
        this.showSlide(nextIndex);
    }
    
    /**
     * Inicia el autoplay
     */
    startAutoplay() {
        this.stopAutoplay();
        
        // No iniciar si el usuario interactuó recientemente
        if (this.state.userInteracted) {
            return;
        }
        
        // Calcular tiempo para el siguiente cambio
        const timeSinceLastTransition = Date.now() - this.state.lastTransitionTime;
        const remainingTime = Math.max(0, this.config.autoplayDelay - timeSinceLastTransition);
        
        // Si queda tiempo, esperar antes de iniciar
        if (remainingTime > 0) {
            // Actualizar barra de progreso
            this.updateProgressBar(1 - (remainingTime / this.config.autoplayDelay));
            
            this.timers.autoplay = setTimeout(() => {
                if (!this.state.userInteracted && !this.state.isAnimating) {
                    this.showNextSlide();
                    this.startAutoplay();
                }
            }, remainingTime);
        } else {
            // Reiniciar barra de progreso y animarla
            this.updateProgressBar(0);
            this.animateProgressBar();
            
            // Programar cambios automáticos
            this.timers.autoplay = setInterval(() => {
                if (!this.state.userInteracted && !this.state.isAnimating) {
                    this.showNextSlide();
                }
            }, this.config.autoplayDelay);
        }
    }
    
    /**
     * Detiene el autoplay
     */
    stopAutoplay() {
        this.clearTimers();
        
        // Detener la animación de la barra de progreso
        if (this.timeBar) {
            this.timeBar.style.transition = 'none';
            this.timeBar.style.width = '0%';
        }
    }
    
    /**
     * Reinicia el autoplay
     */
    resetAutoplay() {
        this.stopAutoplay();
        
        // Pequeño retraso para evitar problemas de timing
        setTimeout(() => {
            if (!this.carousel.matches(':hover')) {
                this.state.userInteracted = false;
                this.startAutoplay();
            }
        }, 100);
    }
    
    /**
     * Limpia todos los timers activos
     */
    clearTimers() {
        if (this.timers.autoplay) {
            clearInterval(this.timers.autoplay);
            clearTimeout(this.timers.autoplay);
            this.timers.autoplay = null;
        }
        
        if (this.timers.animation) {
            clearTimeout(this.timers.animation);
            this.timers.animation = null;
        }
        
        if (this.timers.debounce) {
            clearTimeout(this.timers.debounce);
            this.timers.debounce = null;
        }
    }
    
    /**
     * Actualiza la barra de progreso
     */
    updateProgressBar(progress) {
        if (!this.timeBar) return;
        
        this.timeBar.style.transition = 'none';
        this.timeBar.style.width = `${progress * 100}%`;
    }
    
    /**
     * Anima la barra de progreso
     */
    animateProgressBar() {
        if (!this.timeBar) return;
        
        // Forzar un reflow para resetear la animación
        void this.timeBar.offsetWidth;
        
        this.timeBar.style.transition = `width ${this.config.autoplayDelay}ms linear`;
        this.timeBar.style.width = '100%';
    }
    
    /**
     * Función debounce para eventos frecuentes
     */
    debounce(func, delay) {
        return (...args) => {
            clearTimeout(this.timers.debounce);
            this.timers.debounce = setTimeout(() => func.apply(this, args), delay);
        };
    }
}

// Inicializar el carousel cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    const carousel = new TrendingCarousel();
    
    // Exposición de la instancia para debugging si es necesario
    window.trendingCarousel = carousel;
});
