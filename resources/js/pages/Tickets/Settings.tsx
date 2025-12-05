
import AppLayout from "@/components/layouts/app-layout";
import {Head} from "@inertiajs/react";

import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from "@/components/ui/tabs"
import Statuses from "@/pages/Tickets/Settings/Statuses";

interface Props {
    ticketsStatuses: App.DTO.PaginatedListDto<App.DTO.Ticket.TicketStatusesListDto>
}

export default function Page({ ticketsStatuses }: Props) {

    return (
        <AppLayout>
            <Head title="Ticket Settings" />
            <Tabs defaultValue="tab-1">
                <TabsList className="h-auto rounded-none border-b bg-transparent p-0">
                    <TabsTrigger
                        value="tab-1"
                        className="border-none !bg-transparent data-[state=active]:after:bg-primary relative rounded-none py-2 after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 data-[state=active]:bg-transparent data-[state=active]:shadow-none"
                    >
                        Categories
                    </TabsTrigger>
                    <TabsTrigger
                        value="tab-2"
                        className="border-none !bg-transparent data-[state=active]:after:bg-primary relative rounded-none py-2 after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 data-[state=active]:bg-transparent data-[state=active]:shadow-none"
                    >
                        Topics
                    </TabsTrigger>
                    <TabsTrigger
                        value="tab-3"
                        className="border-none !bg-transparent data-[state=active]:after:bg-primary relative rounded-none py-2 after:absolute after:inset-x-0 after:bottom-0 after:h-0.5 data-[state=active]:bg-transparent data-[state=active]:shadow-none"
                    >
                        Statuses
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="tab-1">
                    <p className="text-muted-foreground p-4 text-center text-xs">
                        Content for Tab 1
                    </p>
                </TabsContent>
                <TabsContent value="tab-2">
                    <p className="text-muted-foreground p-4 text-center text-xs">
                        Content for Tab 2
                    </p>
                </TabsContent>
                <TabsContent value="tab-3" className="py-6">

                </TabsContent>
            </Tabs>
        </AppLayout>
    );
}
