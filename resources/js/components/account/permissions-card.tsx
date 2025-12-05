"use client";

import React from "react";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";

interface PermissionDto {
    name: string;
    title: string;
}

interface PermissionsCardProps {
    permissions: PermissionDto[];
    className?: string;
}

/** Карточка с правами пользователя, сгруппированными по категориям */
export function PermissionsCard({ permissions, className }: PermissionsCardProps) {
    const groupedPermissions = React.useMemo(
        () =>
            Object.entries(
                permissions.reduce<Record<string, PermissionDto[]>>((acc, perm) => {
                    const [group] = perm.name.split(".");
                    acc[group] = acc[group] || [];
                    acc[group].push(perm);
                    return acc;
                }, {})
            ),
        [permissions]
    );

    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle>Permissions</CardTitle>
                <CardDescription>
                    Your assigned permissions grouped by category
                </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                {groupedPermissions.map(([group, groupPermissions], index) => (
                    <div key={group}>
                        <Label className="mb-2 block text-sm font-semibold capitalize">{group}</Label>
                        <div className="flex flex-wrap gap-4">
                            {groupPermissions.map((perm) => (
                                <div key={perm.name} className="flex items-center space-x-2">
                                    <span>✓</span>
                                    <span className="text-sm text-muted-foreground">{perm.title}</span>
                                </div>
                            ))}
                        </div>
                        {index !== groupedPermissions.length - 1 && <Separator className="my-4" />}
                    </div>
                ))}
            </CardContent>
        </Card>
    );
}
