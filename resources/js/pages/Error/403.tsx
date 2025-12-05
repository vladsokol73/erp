import AppLayout from "@/components/layouts/app-layout";
import {Head} from "@inertiajs/react";
export default function Page() {


    return (
        <AppLayout>
            <Head title="Home" />
            <div className="flex mt-64 flex-col gap-6 items-center">
                <img src="/assets/images/favicon-dark.svg" alt="logo" className="h-24 sm:h-32"/>


                <div className="flex flex-col gap-1 items-center">
                    <h1 className="font-bold text-xl sm:text-4xl">403 Forbidden</h1>
                    <p className="text-sm sm:text-lg text-muted-foreground">Tou don't have permission to access on this page.</p>
                </div>
            </div>
        </AppLayout>
    )
}
