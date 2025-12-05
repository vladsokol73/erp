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

interface BasicInformationCardProps {
    email: string;
    name: string;
    roleName: string;
    lastLoginAt: string | null;
    className?: string;
}

export function BasicInformationCard({
                                         email,
                                         name,
                                         roleName,
                                         lastLoginAt,
                                         className,
                                     }: BasicInformationCardProps) {
    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle>Basic Information</CardTitle>
                <CardDescription>
                    Manage your account details and profile information
                </CardDescription>
            </CardHeader>
            <CardContent className="grid grid-cols-2 gap-4">
                <div className="flex flex-col gap-1">
                    <Label>Email</Label>
                    <div className="text-sm text-muted-foreground">{email}</div>
                </div>
                <div className="flex flex-col gap-1">
                    <Label>Name</Label>
                    <div className="text-sm text-muted-foreground">{name}</div>
                </div>
                <div className="flex flex-col gap-1">
                    <Label>Role</Label>
                    <div className="text-sm text-muted-foreground capitalize">{roleName}</div>
                </div>
                <div className="flex flex-col gap-1">
                    <Label>Last Login</Label>
                    <div className="text-sm text-muted-foreground">{lastLoginAt ?? "—"}</div>
                </div>
            </CardContent>
        </Card>
    );
}
