import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
    appId: 'de.dijitalmentor.app',
    appName: 'dijitalmentor',
    webDir: 'build',

    server: {
        androidScheme: 'https',
        url: 'http://192.168.178.118:5176', // Live Reload
        cleartext: true
    },

    plugins: {
        SplashScreen: {
            launchShowDuration: 2000,
            backgroundColor: '#2563eb' // Tailwind blue-600
        }
    }
};

export default config;
