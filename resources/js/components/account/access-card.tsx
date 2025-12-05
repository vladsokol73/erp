import React from "react";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";

interface AccessCardProps {
    countries: string[];
    channels: string[];
    operators: string[];
    tags: string[];
    className?: string;
}

function BadgeList({ items }: { items: string[] }) {
    if (!items || items.length === 0) return <span>—</span>;

    return (
        <div className="flex flex-wrap gap-1">
            {items.map((item, idx) => (
                <Badge key={`${item}-${idx}`} variant="secondary" className="capitalize">
                    {item}
                </Badge>
            ))}
        </div>
    );
}

export function AccessCard({
                               countries,
                               channels,
                               operators,
                               tags,
                               className,
                           }: AccessCardProps) {
    return (
        <Card className={className}>
            <CardHeader>
                <CardTitle>Access</CardTitle>
                <CardDescription>
                    View your assigned countries, channels and operators
                </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
                <div className="flex flex-col gap-2">
                    <Label>Available Countries</Label>
                    <BadgeList items={countries} />
                </div>

                <Separator className="my-4" />

                <div className="flex flex-col gap-2">
                    <Label>Available Channels</Label>
                    <BadgeList items={channels} />
                </div>

                <Separator className="my-4" />

                <div className="flex flex-col gap-2">
                    <Label>Available Operators</Label>
                    {/* для операторов в макете была чуть большая дистанция между баджами */}
                    <div className="flex flex-wrap gap-2">
                        {operators?.length
                            ? operators.map((op, idx) => (
                                <Badge key={`${op}-${idx}`} variant="secondary" className="capitalize">
                                    {op}
                                </Badge>
                            ))
                            : "—"}
                    </div>
                </div>

                <Separator className="my-4" />

                <div className="flex flex-col gap-2">
                    <Label>Available Tags</Label>
                    <div className="flex flex-wrap gap-2">
                        {tags?.length
                            ? tags.map((tag, idx) => (
                                <Badge key={`${tag}-${idx}`} variant="secondary" className="capitalize">
                                    {tag}
                                </Badge>
                            ))
                            : "—"}
                    </div>
                </div>
            </CardContent>
        </Card>
    );
}
