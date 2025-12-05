import {
    z,
    ZodString,
    ZodNumber,
    ZodDate,
    ZodArray,
    ZodTypeAny,
} from "zod";

export function generateZodSchemaFromRules(
    rules: App.DTO.Ticket.ValidationRuleDto[],
    isRequired: boolean,
    fieldType: string
): ZodTypeAny {
    // FILE
    if (fieldType === "file") {
        let schema = z.union([
            z.instanceof(File, { message: "Must be a file" }),
            z.string().url("Must be a valid URL"),
        ]);

        return isRequired ? schema : schema.optional().nullable();
    }

    // NUMBER
    if (fieldType === "number") {
        let schema: ZodNumber = z.number({ invalid_type_error: "Must be a number" });

        for (const rule of rules) {
            const numVal = Number(rule.value);
            switch (rule.type) {
                case "min_number":
                    schema = schema.min(numVal, `Minimum value is ${numVal}`);
                    break;
                case "max_number":
                    schema = schema.max(numVal, `Maximum value is ${numVal}`);
                    break;
            }
        }

        return isRequired ? schema : schema.optional();
    }

    // MULTISELECT
    if (fieldType === "multiselect") {
        let schema: ZodArray<ZodString> = z.array(z.string(), {
            invalid_type_error: "Must be an array of strings",
        });

        return isRequired
            ? schema.min(1, "This field is required")
            : schema.optional();
    }

    // DATE
    if (fieldType === "date") {
        let schema: ZodDate = z.date({ invalid_type_error: "Must be a valid date" });

        for (const rule of rules) {
            const val = rule.value;
            switch (rule.type) {
                case "min_date":
                    schema = schema.min(new Date(val), `Date must be after ${val}`);
                    break;
                case "max_date":
                    schema = schema.max(new Date(val), `Date must be before ${val}`);
                    break;
            }
        }

        return isRequired ? schema : schema.optional();
    }

    // COUNTRY (ID — number, >= 1)
    if (fieldType === "country" ) {
        let schema: ZodNumber = z
            .number({ invalid_type_error: "Must be a number" })
            .int("Country ID must be an integer")
            .min(1, "Select a country");

        return isRequired ? schema : schema.optional();
    }

    if (fieldType === "project") {
        let schema: ZodNumber = z
            .number({ invalid_type_error: "Must be a number" })
            .int("Project ID must be an integer")
            .min(1, "Select a project");

        return isRequired ? schema : schema.optional();
    }

    // TEXT, TEXTAREA, SELECT
    let baseSchema = z.string();

    for (const rule of rules) {
        const val = rule.value;
        switch (rule.type) {
            case "email":
                baseSchema = baseSchema.email("Invalid email format");
                break;
            case "url":
                baseSchema = baseSchema.url("Invalid URL");
                break;
            case "min_length":
                baseSchema = baseSchema.min(Number(val), `Minimum length is ${val}`);
                break;
            case "max_length":
                baseSchema = baseSchema.max(Number(val), `Maximum length is ${val}`);
                break;
        }
    }

    let finalSchema: ZodTypeAny = baseSchema;

    for (const rule of rules) {
        const val = rule.value;
        switch (rule.type) {
            case "contains":
                finalSchema = finalSchema.refine(
                    (s) => s?.includes(String(val)),
                    { message: `Must contain "${val}"` }
                );
                break;
            case "not_contains":
                finalSchema = finalSchema.refine(
                    (s) => !s?.includes(String(val)),
                    { message: `Must not contain "${val}"` }
                );
                break;
        }
    }

    if (!isRequired) {
        finalSchema = finalSchema.optional();
    }

    return finalSchema;

}
