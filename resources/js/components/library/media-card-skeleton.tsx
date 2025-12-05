import React from "react";
import { Skeleton } from "@/components/ui/skeleton";

const MediaCardSkeleton = () => {
    return (
        <div data-slot="card"
             className="bg-card text-card-foreground flex flex-col gap-6 rounded-xl border py-6 shadow-sm">
            <div
                 className="grid items-start gap-1.5 mb-1.5 px-6">
                <div className="flex justify-between items-center">
                    <div className="flex flex-col gap-2">
                        <div className="flex gap-4">
                            <Skeleton className="size-10 rounded-full"/>
                            <div className="flex flex-col gap-1">
                                <Skeleton className="h-6 w-32"/>
                                <Skeleton className="h-5 w-48"/>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Skeleton className="h-[22px] w-20"/>
                            <Skeleton className="h-[22px] w-24"/>
                        </div>
                    </div>
                    <Skeleton className="size-9"/>
                </div>
            </div>
            <div data-slot="card-content" className="px-6">
                <Skeleton className="h-64"/>
            </div>
            <div data-slot="card-footer" className="flex items-center px-6 [.border-t]:pt-6">
                <div className="flex flex-col gap-4 w-full">
                    <div className="flex items-center gap-4">
                        <div className="flex items-center gap-2">
                            <Skeleton className="size-9 rounded-full"/>
                            <Skeleton className="h-4 w-8"/>
                        </div>
                        <div className="flex items-center gap-2">
                            <Skeleton className="size-9 rounded-full"/>
                            <Skeleton className="h-4 w-6"/>
                        </div>
                    </div>
                    <div className="flex w-full justify-between items-center">
                        <Skeleton className="h-9 w-[112px]"/>
                        <Skeleton className="h-9 w-[113px]"/>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MediaCardSkeleton;
