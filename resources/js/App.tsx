import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import React from 'react';

import "../css/globals.css";
import "./bootstrap";

import { Toaster } from "@/components/ui/sonner"
import { TooltipProvider } from "@/components/ui/tooltip"
import { Confirmer } from './components/ui/confirmer';

const appName = import.meta.env.VITE_APP_NAME || 'Gteam';

createInertiaApp({
    title: (title: string) => `${title} - ${appName}`,
    resolve: (name: string) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob<any>('./pages/**/*.tsx')
        ),
    setup({ el, App, props }: { el: HTMLElement, App: React.ComponentType<any>, props: any }) {
        const root = createRoot(el);

        root.render(
            <TooltipProvider>
                <App {...props} />
                <Toaster />
                <Confirmer />
            </TooltipProvider>
        );
    },
    progress: {
        color: '#006fee'
    }
}).catch(error => {
    console.error('Failed to initialize Inertia app:', error);
});
