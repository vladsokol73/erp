import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { Separator } from "@/components/ui/separator";

interface Props {
    value: App.DTO.User.PermissionDto[];
    onChange: (value:  App.DTO.User.PermissionDto[]) => void;
    permissions: App.DTO.User.PermissionDto[];
    error?: string;
}

export function PermissionsField({ value, onChange, permissions, error }: Props) {
    const grouped = permissions.reduce<Record<string,  App.DTO.User.PermissionDto[]>>((acc, perm) => {
        const [group] = perm.name.split(".");
        acc[group] = acc[group] || [];
        acc[group].push(perm);
        return acc;
    }, {});

    const isChecked = (perm:  App.DTO.User.PermissionDto) => {
        return value.some((v) => v.name === perm.name);
    };

    const togglePermission = (perm: App.DTO.User.PermissionDto) => {
        const updated = isChecked(perm)
            ? value.filter((v) => v.name !== perm.name)
            : [...value, perm];

        onChange(updated);
    };

    return (
        <div className="space-y-4 overflow-y-auto max-h-[450px]">
            {Object.entries(grouped).map(([group, groupPermissions]) => (
                <div key={group}>
                    <Label className="mb-2 block text-sm font-semibold capitalize">{group}</Label>
                    <div className="flex flex-wrap gap-4">
                        {groupPermissions.map((perm) => (
                            <div key={perm.name} className="flex items-center space-x-2">
                                <Checkbox
                                    id={perm.name}
                                    checked={isChecked(perm)}
                                    onCheckedChange={() => togglePermission(perm)}
                                />
                                <Label htmlFor={perm.name} className="capitalize">
                                    {perm.title}
                                </Label>
                            </div>
                        ))}
                    </div>
                    <Separator className="my-4" />
                </div>
            ))}

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
}
