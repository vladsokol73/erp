import AppLayout from "@/components/layouts/app-layout";
import {Head} from "@inertiajs/react";
export default function Page() {


    return (
        <AppLayout>
            <Head title="Home" />
            <div className="flex mt-64 flex-col gap-6 items-center">
                <h1 className="text-3xl sm:text-6xl">Coming soon</h1>
                <img src="/assets/images/favicon-dark.svg" alt="logo" className="h-48 sm:h-96"/>
            </div>
        </AppLayout>
    )
}
