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
            isStopped: false,
            lastTransitionTime: 0,
            touchStartX: 0,
            isTouching: false,
            errorCount: 0,
            maxRetries: 3
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
            debounce: null,
            progressBar: null,
            resetStop: null,
            errorReset: null
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
        
        // Aplicar clase global que habilita el scroll para todos los slides
        this.enableScrollingForAllSlides();
        
        // Preparar el slide inicial
        this.preloadCriticalImages();
        this.applyInitialStyles();
        
        // Configurar eventos
        this.setupEventListeners();
        
        // Iniciar autoplay después de que todo esté listo
        this.state.lastTransitionTime = Date.now();
        setTimeout(() => {
            // Verificar que aún existe el carousel (por si se ha desmontado)
            if (this.carousel && document.body.contains(this.carousel)) {
                this.startAutoplay();
            }
        }, 1000);
        
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
        if (!this.slides || this.slides.length === 0) return;
        
        // Ocultar todos los slides por defecto
        this.slides.forEach(slide => {
            slide.style.opacity = '0';
            slide.style.visibility = 'hidden';
            slide.style.zIndex = '0';
            slide.classList.remove('active');
            
            // Resetear estilos de contenido
            const content = slide.querySelector('.content');
            if (content) {
                if (window.matchMedia('(max-width: 768px)').matches) {
                    content.style.transform = 'translate(-50%, -50%) translateX(30px)';
                } else {
                    content.style.transform = 'translateY(-50%) translateX(30px)';
                }
                content.style.opacity = '0';
                
                // Resetear elementos dentro del contenido
                const elements = [
                    content.querySelector('.topic'),
                    content.querySelector('.title'),
                    content.querySelector('.des'),
                    content.querySelector('.metrics'),
                    content.querySelector('.buttons')
                ];
                
                elements.forEach(el => {
                    if (el) {
                        el.style.opacity = '0';
                        el.style.transform = 'translateY(20px)';
                    }
            });
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
                if (window.matchMedia('(max-width: 768px)').matches) {
                    content.style.transform = 'translate(-50%, -50%) translateX(0)';
                } else {
                    content.style.transform = 'translateY(-50%) translateX(0)';
                }
                content.style.opacity = '1';
                
                // Animar elementos dentro del contenido con secuencia
                const scrollableContent = content.querySelector('.scrollable-content');
                if (scrollableContent) {
                    // Asegurar que el scrollable content tiene su overflow habilitado
                    scrollableContent.style.overflow = 'auto';
                    scrollableContent.style.overflowY = 'auto';
                    
                    // Reiniciar la posición de scroll
                    scrollableContent.scrollTop = 0;
                    
                    const elements = [
                        scrollableContent.querySelector('.topic'),
                        scrollableContent.querySelector('.title'),
                        scrollableContent.querySelector('.des'),
                        scrollableContent.querySelector('.metrics')
                    ];
                    
                    elements.forEach((el, index) => {
                        if (el) {
                            setTimeout(() => {
                                el.style.opacity = '1';
                                el.style.transform = 'translateY(0)';
                                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                            }, el.classList.contains('des') ? 
                               150 + (index * 80) : // Más retraso para la descripción
                               100 + (index * 70));
                        }
                    });
                }
                
                // Asegurar que los botones sean visibles inmediatamente
                const buttonContainer = content.querySelector('.button-container');
                if (buttonContainer) {
                    buttonContainer.style.opacity = '1';
                    
                    const buttons = buttonContainer.querySelector('.buttons');
                    if (buttons) {
                        buttons.style.opacity = '1';
                        buttons.style.transform = 'translateY(0)';
                        
                        const buttonElements = buttons.querySelectorAll('button');
                        buttonElements.forEach(btn => {
                            btn.style.opacity = '1';
                            btn.style.transform = 'translateY(0)';
                        });
                    }
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
        
        // No hacer nada si ya estamos en ese slide o hay una animación en curso
        if (index === this.state.currentIndex || this.state.isAnimating) {
            return;
        }
        
        // Activar flag de animación
        this.state.isAnimating = true;
        console.log(`Transición iniciada: ${this.state.currentIndex} -> ${index}`);
        
        // Guardar índice anterior y actualizar actual
        this.state.previousIndex = this.state.currentIndex;
        this.state.currentIndex = index;
        this.state.lastTransitionTime = Date.now();
        
        // Detener cualquier autoplay activo
        this.stopAutoplay();
        
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
        
        // Habilitar scroll en el slide entrante de inmediato
        const nextContent = nextSlide.querySelector('.content');
        if (nextContent) {
            const nextScrollable = nextContent.querySelector('.scrollable-content');
            if (nextScrollable) {
                // Forzar habilitación de scroll
                nextScrollable.style.overflow = 'auto';
                nextScrollable.style.overflowY = 'auto';
                nextScrollable.classList.add('scroll-enabled');
                nextScrollable.scrollTop = 0;
            }
        }
        
        // Fase 2: Configurar slide entrante
        nextSlide.style.visibility = 'visible';
        nextSlide.style.opacity = '0';
        nextSlide.classList.add('active');
        
        // Fase 3: Animar salida del slide actual y entrada del nuevo
        requestAnimationFrame(() => {
            // Animar salida del contenido actual
            const currentContent = currentSlide.querySelector('.content');
            if (currentContent) {
                if (window.matchMedia('(max-width: 768px)').matches) {
                    currentContent.style.opacity = '0';
                    currentContent.style.transform = 'translate(-50%, -50%) translateX(-30px)';
                } else {
                    currentContent.style.opacity = '0';
                    currentContent.style.transform = 'translateY(-50%) translateX(-30px)';
                }
            }
            
            // Comenzar a desvanecer slide actual
            currentSlide.style.opacity = '0';
            
            // Animar entrada del nuevo slide
            setTimeout(() => {
                nextSlide.style.opacity = '1';
                
                // Animar contenido del nuevo slide
                if (nextContent) {
                    nextContent.style.opacity = '0';
                    
                    if (window.matchMedia('(max-width: 768px)').matches) {
                        nextContent.style.transform = 'translate(-50%, -50%) translateX(30px)';
                    } else {
                        nextContent.style.transform = 'translateY(-50%) translateX(30px)';
                    }
                    
                    setTimeout(() => {
                        if (window.matchMedia('(max-width: 768px)').matches) {
                            nextContent.style.transform = 'translate(-50%, -50%) translateX(0)';
    } else {
                            nextContent.style.transform = 'translateY(-50%) translateX(0)';
                        }
                        nextContent.style.opacity = '1';
                        
                        // Animar elementos dentro del contenido con secuencia, excepto botones
                        const scrollableContent = nextContent.querySelector('.scrollable-content');
                        if (scrollableContent) {
                            // Triple verificación para asegurar que el overflow esté habilitado
                            scrollableContent.style.overflow = 'auto';
                            scrollableContent.style.overflowY = 'auto';
                            scrollableContent.classList.add('scroll-enabled');
                            
                            // Reiniciar la posición de scroll
                            scrollableContent.scrollTop = 0;
                            
                            const elements = [
                                scrollableContent.querySelector('.topic'),
                                scrollableContent.querySelector('.title'),
                                scrollableContent.querySelector('.des'),
                                scrollableContent.querySelector('.metrics')
                            ];
                            
                            elements.forEach((el, index) => {
                                if (el) {
                                    el.style.opacity = '0';
                                    el.style.transform = 'translateY(20px)';
                                    el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                                    
                                    // Para la descripción, usar un retraso adicional
                                    const delay = el.classList.contains('des') ? 
                                        150 + (index * 80) : // Más retraso para la descripción
                                        100 + (index * 70);
                                    
                                    setTimeout(() => {
                                        el.style.opacity = '1';
                                        el.style.transform = 'translateY(0)';
                                    }, delay);
                                }
                            });
                        }
                        
                        // Asegurar que los botones sean visibles inmediatamente
                        const buttonContainer = nextContent.querySelector('.button-container');
                        if (buttonContainer) {
                            buttonContainer.style.opacity = '1';
                            
                            const buttons = buttonContainer.querySelector('.buttons');
                            if (buttons) {
                                buttons.style.opacity = '1';
                                buttons.style.transform = 'translateY(0)';
                                
                                // Asegurar que cada botón individual esté visible
                                const buttonElements = buttons.querySelectorAll('button');
                                buttonElements.forEach(btn => {
                                    btn.style.opacity = '1';
                                    btn.style.transform = 'translateY(0)';
                                });
                            }
                        }
                    }, 400);
                }
                
                // Programar finalización de transición
                setTimeout(() => {
                    this.finalizeTransition();
                }, 1000); // Tiempo total de la transición
            }, 200);
        });
    }
    
    /**
     * Finaliza la transición y limpia
     */
    finalizeTransition() {
        // Remover clase de transición
        this.carousel.classList.remove('transitioning');
        
        // Actualizar miniaturas
        this.updateThumbnails();
        
        // Resetear flags
        this.state.isAnimating = false;
        
        // Realizar limpieza de slides
        this.slides.forEach(slide => {
            const isActive = slide.classList.contains('active');
            
            if (isActive) {
                // Para el slide activo, garantizar que el overflow está habilitado
                const content = slide.querySelector('.content');
                if (content) {
                    // Garantizar que el contenido desplazable tiene su overflow habilitado
                    const scrollableContent = content.querySelector('.scrollable-content');
                    if (scrollableContent) {
                        // Método directo: establecer estilos importantes que no pueden ser sobrescritos
                        scrollableContent.style.setProperty('overflow', 'auto', 'important');
                        scrollableContent.style.setProperty('overflow-y', 'auto', 'important');
                        scrollableContent.classList.add('scroll-enabled');
                    }
                    
                    const buttons = content.querySelector('.buttons');
                    if (buttons) {
                        buttons.style.opacity = '1';
                        buttons.style.transform = 'translateY(0)';
                        
                        const buttonElements = buttons.querySelectorAll('button');
                        buttonElements.forEach(btn => {
                            btn.style.opacity = '1';
                            btn.style.transform = 'translateY(0)';
                        });
                    }
                }
            } else {
                slide.classList.remove('active');
                slide.style.visibility = 'hidden';
                slide.style.opacity = '0';
                slide.style.zIndex = '0';
                
                // Resetear estilos de contenido pero mantener overflow habilitado
                const content = slide.querySelector('.content');
                if (content) {
                    if (window.matchMedia('(max-width: 768px)').matches) {
                        content.style.transform = 'translate(-50%, -50%) translateX(30px)';
                    } else {
                        content.style.transform = 'translateY(-50%) translateX(30px)';
                    }
                    content.style.opacity = '0';
                    content.style.transition = '';
                    
                    // Mantener overflow habilitado incluso en slides no activos
                    const scrollableContent = content.querySelector('.scrollable-content');
                    if (scrollableContent) {
                        // Reiniciar la posición de scroll pero mantener overflow
                        scrollableContent.scrollTop = 0;
                        scrollableContent.style.setProperty('overflow', 'auto', 'important');
                        scrollableContent.style.setProperty('overflow-y', 'auto', 'important');
                        scrollableContent.classList.add('scroll-enabled');
                    }
                    
                    // Resetear elementos dentro del contenido
                    const elements = [
                        content.querySelector('.topic'),
                        content.querySelector('.title'),
                        content.querySelector('.des'),
                        content.querySelector('.metrics')
                    ];
                    
                    elements.forEach(el => {
                        if (el) {
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(20px)';
                            el.style.transition = '';
                        }
                    });
                }
            }
        });
        
        // Volver a habilitar el scroll en todos los slides para garantizar consistencia
        this.enableScrollingForAllSlides();
        
        console.log(`Transición finalizada, slide activo: ${this.state.currentIndex}`);
        
        // Reiniciar autoplay si está activado
        if (this.state.autoplayEnabled) {
            this.resetAutoplay();
        }
    }
    
    /**
     * Actualiza las miniaturas para resaltar la actual
     */
    updateThumbnails() {
        if (!this.thumbnails || this.thumbnails.length === 0) return;
        
        const currentIndex = this.state.currentIndex;
        
        // Limpiar clases activas y aplicar estilos neutrales
        this.thumbnails.forEach((thumb, idx) => {
            // Quitar clase activa de todas las miniaturas
            thumb.classList.remove('active');
            
            // Aplicar filtro y bordes neutrales
            thumb.style.transition = 'all 0.3s ease';
            thumb.style.filter = 'brightness(0.65) saturate(0.8)';
            thumb.style.transform = 'translateX(0)';
            thumb.style.borderColor = 'transparent';
            
            // Reducir opacidad del texto en todas las miniaturas
            const titleEl = thumb.querySelector('.title');
            const descEl = thumb.querySelector('.description');
            
            if (titleEl) {
                titleEl.style.transition = 'all 0.3s ease';
                titleEl.style.opacity = '0.7';
            }
            
            if (descEl) {
                descEl.style.transition = 'all 0.3s ease';
                descEl.style.opacity = '0.6';
            }
        });
        
        // Aplicar estilo a la miniatura activa con un efecto más notable
        const activeThumb = this.thumbnails[currentIndex];
        if (activeThumb) {
            // Agregar clase activa
            activeThumb.classList.add('active');
            
            // Aplicar efectos visuales destacados
            activeThumb.style.filter = 'brightness(1) saturate(1.2)';
            activeThumb.style.transform = 'translateX(-5px) scale(1.05)';
            activeThumb.style.borderColor = '#1976d2';
            activeThumb.style.boxShadow = '0 6px 12px rgba(0, 0, 0, 0.4)';
            
            // Hacer que el texto sea más visible
            const titleEl = activeThumb.querySelector('.title');
            const descEl = activeThumb.querySelector('.description');
            
            if (titleEl) {
                titleEl.style.opacity = '1';
                titleEl.style.transform = 'translateY(0)';
            }
            
            if (descEl) {
                descEl.style.opacity = '0.9';
                descEl.style.transform = 'translateY(0)';
            }
            
            // Si estamos en móvil, asegurar que la miniatura activa esté visible
            if (window.matchMedia('(max-width: 768px)').matches) {
                // Dar tiempo para que se apliquen las clases antes de hacer scroll
                setTimeout(() => {
                    // Obtener el contenedor de miniaturas
                    const thumbContainer = document.querySelector('.carousel .thumbnail');
                    if (thumbContainer && activeThumb) {
                        // Calcular posición para centrar
                        const containerWidth = thumbContainer.offsetWidth;
                        const thumbPosition = activeThumb.offsetLeft;
                        const thumbWidth = activeThumb.offsetWidth;
                        const scrollPos = thumbPosition - (containerWidth / 2) + (thumbWidth / 2);
                        
                        // Hacer scroll suave
                        thumbContainer.scrollTo({
                            left: scrollPos,
                            behavior: 'smooth'
                        });
                    }
                }, 50); // Pequeño retraso para asegurar que los elementos ya tienen sus clases
            }
        }
        
        // Opcionalmente, animar ligeramente las miniaturas adyacentes
        const prevIndex = (currentIndex - 1 + this.state.totalSlides) % this.state.totalSlides;
        const nextIndex = (currentIndex + 1) % this.state.totalSlides;
        
        if (this.thumbnails[prevIndex]) {
            this.thumbnails[prevIndex].style.filter = 'brightness(0.75) saturate(0.9)';
        }
        
        if (this.thumbnails[nextIndex]) {
            this.thumbnails[nextIndex].style.filter = 'brightness(0.75) saturate(0.9)';
        }
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
        // Limpieza previa de timers para evitar duplicados
        this.stopAutoplay();
        
        // No iniciar si el carousel no existe, está detenido o el usuario interactuó recientemente
        if (!this.carousel || this.state.userInteracted || this.state.isStopped || !document.body.contains(this.carousel)) {
            return;
        }
        
        // Verificar si el documento está visible
        if (document.hidden) {
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
                if (!this.state.userInteracted && !this.state.isAnimating && !this.state.isStopped) {
                    this.showNextSlide();
                    this.startAutoplay();
                }
            }, remainingTime);
        } else {
            // Reiniciar barra de progreso y animarla
            this.updateProgressBar(0);
            this.animateProgressBar();
            
            // Programar cambios automáticos
            this.timers.autoplay = setTimeout(() => {
                if (!this.state.userInteracted && !this.state.isAnimating && !this.state.isStopped) {
                    this.showNextSlide();
                    // Llamar recursivamente para continuar el autoplay
                    setTimeout(() => this.startAutoplay(), 100);
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
        
        // Marcar como detenido temporalmente
        this.state.isStopped = true;
        
        // Después de un corto período, permitir que se reinicie si es necesario
        clearTimeout(this.timers.resetStop);
        this.timers.resetStop = setTimeout(() => {
            this.state.isStopped = false;
        }, 200);
    }
    
    /**
     * Reinicia el autoplay
     */
    resetAutoplay() {
        this.stopAutoplay();
        
        // Pequeño retraso para evitar problemas de timing
        setTimeout(() => {
            // Solo reiniciar si el ratón no está sobre el carousel y si el documento es visible
            if (!this.carousel.matches(':hover') && !document.hidden) {
                this.state.userInteracted = false;
                this.startAutoplay();
            }
        }, 300);
    }
    
    /**
     * Limpia todos los timers activos
     */
    clearTimers() {
        Object.keys(this.timers).forEach(key => {
            if (this.timers[key]) {
                clearTimeout(this.timers[key]);
                clearInterval(this.timers[key]);
                this.timers[key] = null;
            }
        });
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
        
        // Aplicar transición para animación
        requestAnimationFrame(() => {
            this.timeBar.style.transition = `width ${this.config.autoplayDelay}ms linear`;
            this.timeBar.style.width = '100%';
        });
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
    
    /**
     * Maneja errores durante la animación y recupera el carrusel
     */
    handleError() {
        this.state.errorCount++;
        console.warn(`Error en el carrusel (${this.state.errorCount}/${this.state.maxRetries})`);
        
        // Forzar finalización si hay error
        this.clearTimers();
        
        if (this.state.errorCount >= this.state.maxRetries) {
            console.error('Demasiados errores, reiniciando completamente el carrusel');
            this.state.isAnimating = false;
            this.state.isStopped = true;
            
            // Forzar un estado limpio después de un retraso
            setTimeout(() => {
                try {
                    // Reinicializar todo el carrusel
                    this.applyInitialStyles();
                    this.state.errorCount = 0;
                    this.state.isStopped = false;
                    this.startAutoplay();
                } catch (e) {
                    console.error('Error fatal en el carrusel, deshabilitando:', e);
                    this.disableCarousel();
                }
            }, 1000);
        } else {
            // Intento de recuperación simple
            this.state.isAnimating = false;
            setTimeout(() => this.finalizeTransition(), 500);
        }
    }
    
    /**
     * En caso de error fatal, deshabilita el carrusel
     */
    disableCarousel() {
        if (!this.carousel) return;
        
        // Eliminar todos los eventos
        this.carousel.removeEventListener('mouseenter', this.onCarouselMouseEnter);
        this.carousel.removeEventListener('mouseleave', this.onCarouselMouseLeave);
        this.carousel.removeEventListener('touchstart', this.onTouchStart);
        this.carousel.removeEventListener('touchmove', this.onTouchMove);
        this.carousel.removeEventListener('touchend', this.onTouchEnd);
        
        if (this.prevBtn) {
            this.prevBtn.removeEventListener('click', this.onPrevButtonClick);
        }
        
        if (this.nextBtn) {
            this.nextBtn.removeEventListener('click', this.onNextButtonClick);
        }
        
        // Detener completamente el autoplay
        this.stopAutoplay();
        
        // Mostrar al menos un slide
        if (this.slides.length > 0) {
            const activeSlide = this.slides[0];
            activeSlide.style.opacity = '1';
            activeSlide.style.visibility = 'visible';
        }
        
        console.warn('Carrusel deshabilitado por errores.');
    }
    
    /**
     * Habilita el scroll vertical para todos los slides
     */
    enableScrollingForAllSlides() {
        if (!this.slides) return;
        
        this.slides.forEach(slide => {
            const content = slide.querySelector('.content');
            if (content) {
                const scrollableContent = content.querySelector('.scrollable-content');
                if (scrollableContent) {
                    // Establecer overflow y asegurar que no se resetee por otras operaciones
                    scrollableContent.style.overflow = 'auto';
                    scrollableContent.style.overflowY = 'auto';
                    
                    // Agregar una clase para identificar que el scroll está habilitado
                    scrollableContent.classList.add('scroll-enabled');
                    
                    // Resetear posición de scroll
                    scrollableContent.scrollTop = 0;
                }
            }
        });
    }
}

// Inicializar el carousel cuando el DOM esté cargado
document.addEventListener('DOMContentLoaded', () => {
    const carousel = new TrendingCarousel();
    
    // Exposición de la instancia para debugging si es necesario
    window.trendingCarousel = carousel;
});
