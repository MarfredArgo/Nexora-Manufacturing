if (window.tailwind) {
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    primary: {
                        DEFAULT: '#ff6b00',
                        hover: '#e56000',
                        glow: 'rgba(255, 107, 0, 0.5)'
                    },
                    dark: {
                        bg: '#050505',
                        surface: '#121212'
                    }
                },
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                }
            }
        }
    }
}
