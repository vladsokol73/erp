"use client";

import AppLayout from "@/components/layouts/app-layout";
import { Head } from "@inertiajs/react";
import useApi from "@/hooks/use-api";
import { route } from "ziggy-js";

import React from "react";
import { ChangePasswordCard, PasswordFormValues } from "@/components/account/change-password-card";
import { AccessCard } from "@/components/account/access-card";
import { BasicInformationCard } from "@/components/account/basic-information-card";
import { PermissionsCard } from "@/components/account/permissions-card";
import { TwoFactorCard } from "@/components/account/two-factor-card";
import {TelegramConnectCard} from "@/components/account/telegram-connect-card";

interface Props {
    user: App.DTO.User.UserProfileDto;
}

export default function SettingsPage({ user }: Props) {
    const api = useApi();

    const [isSavingPassword, setIsSavingPassword] = React.useState(false);
    const [serverMessage, setServerMessage] = React.useState<string | null>(null);


    const handleChangePassword = async (values: PasswordFormValues) => {
        setIsSavingPassword(true);
        setServerMessage(null);

        await api.put(route("account.password.reset"), values, {
            onSuccess: () => {
                setIsSavingPassword(false);
                setServerMessage("Password has been changed successfully.");
            },
            onError: (error) => {
                setIsSavingPassword(false);

                console.log(error);

                const errors = (error as any)?.errors;

                if (errors && typeof errors === "object") {
                    const firstField = Object.keys(errors)[0];
                    const firstMsg = Array.isArray(errors[firstField]) ? errors[firstField][0] : String(errors[firstField]);
                    setServerMessage(firstMsg || "Failed to change password.");

                    return;
                }

                // fallback для неожиданных ошибок
                setServerMessage(
                    typeof (error as any)?.message === "string"
                        ? (error as any).message
                        : "Failed to change password."
                );
            }
        });
    };

    return (
        <AppLayout>
            <Head title="Account Settings" />
            <h1 className="text-2xl font-bold mb-4">Account Settings</h1>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {/* Левая колонка */}
                <div className="col-span-2 space-y-6">
                    <BasicInformationCard
                        email={user.email}
                        name={user.name}
                        roleName={user.role.name}
                        lastLoginAt={user.last_login_at}
                    />

                    <AccessCard
                        countries={user.available_countries}
                        channels={user.available_channels}
                        operators={user.available_operators}
                        tags={user.available_tags}
                    />

                    <TwoFactorCard
                        initialEnabled={!!user.two_factor}
                    />

                    <TelegramConnectCard
                        initialConnected={Boolean(user.telegram_connected)}
                    />

                </div>

                {/* Правая колонка */}
                <div className="space-y-6">
                    <PermissionsCard permissions={user.permissions} />

                    {/* Change Password — вся логика сабмита в родителе */}
                    <ChangePasswordCard
                        onSubmit={handleChangePassword}
                        isSaving={isSavingPassword}
                        serverMessage={serverMessage}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
