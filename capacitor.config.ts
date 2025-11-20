import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
    appId: 'de.dijitalmentor.app',
    appName: 'dijitalmentor',
    webDir: 'build',

    server: {
        androidScheme: 'https', // HTTPS şart (security)
        // Development sırasında:
        // url: 'http://192.168.1.100:5173',
        // cleartext: true
    },

    plugins: {
        SplashScreen: {
            launchShowDuration: 2000,
            backgroundColor: '#2563eb' // Tailwind blue-600
        }
    }
};

export default config;
