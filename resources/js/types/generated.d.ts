declare namespace App.DTO {
export type ApiTokenDto = {
id: number;
service: string;
email: string;
};
export type ApiTokenListDto = {
id: number;
service: string;
email: string;
token: string;
created_at: string;
};
export type ChannelDto = {
id: number;
name: string | null;
};
export type ChannelListDto = {
id: number;
channel_id: number;
name: string | null;
has_ai_retention: boolean;
created_at: string;
};
export type CommentDto = {
id: number;
comment: string;
user_name: string;
created_at: string;
};
export type CountryDto = {
id: number;
name: string;
iso: string | null;
img: string | null;
};
export type OperatorDto = {
id: number;
name: string | null;
};
export type OperatorListDto = {
id: number;
operator_id: number;
name: string | null;
has_ai_retention: boolean;
created_at: string;
};
export type ProjectDto = {
id: number;
name: string;
description: string;
currency: string;
country_id: number;
};
}
declare namespace App.DTO.Client {
export type ClientDetailsDto = {
id: number;
clickid: string | null;
tg_id: number | null;
source_id: string | null;
prod_id: string | null;
player_id: string | null;
reg: boolean;
dep: boolean;
redep: boolean;
reg_date: string | null;
dep_date: string | null;
redep_date: string | null;
dep_sum: number | null;
is_pb: boolean;
is_pb_date: string | null;
pb_bot_name: string | null;
pb_last_mssg: string | null;
pb_channelsub: boolean;
pb_channelsub_date: string | null;
is_c2d: boolean;
is_c2d_date: string | null;
c2d_channel_id: string | null;
c2d_tags: string | null;
c2d_last_mssg: string | null;
geo_click: string | null;
lang: string | null;
type: string | null;
user_agent: string | null;
oc: string | null;
ver_oc: string | null;
model: string | null;
browser: string | null;
ip: string | null;
sub1: string | null;
sub2: string | null;
sub3: string | null;
sub4: string | null;
sub5: string | null;
sub6: string | null;
sub7: string | null;
sub8: string | null;
sub9: string | null;
sub10: string | null;
sub11: string | null;
sub12: string | null;
sub13: string | null;
sub14: string | null;
sub15: string | null;
c2d_client_id: string | null;
creative: App.DTO.Creative.CreativeDto | null;
};
export type ClientListDto = {
id: number;
clickid: string | null;
tg_id: string | null;
c2d_channel_id: string | null;
};
export type ClientLogDto = {
id: number;
webhook_event: string;
webhook_data: Array<any>;
task_status: string | null;
worker_id: number | null;
started_at: string | null;
finished_at: string | null;
result: string | null;
};
export type FailedJobListDto = {
id: number;
connection: string;
queue: string;
failed_at: string;
exception: string;
};
}
declare namespace App.DTO.Creative {
export type CreativeDto = {
id: number;
code: string;
url: string;
type: string;
country: App.DTO.CountryDto;
user_id: number;
created_at: string;
thumbnail: string | null;
};
export type CreativeListDto = {
id: number;
code: string;
url: string;
type: string;
country: App.DTO.CountryDto;
likes_count: number;
dislikes_count: number;
comments: Array<any>;
resolution: string | null;
user_id: number;
created_at: string;
statistic: CreativeStatisticDto;
tags: TagDto[];
thumbnail: string | null;
user_liked: boolean;
user_disliked: boolean;
favorite: boolean;
};
export type CreativeStatisticDto = {
code: string;
clicks: number;
ctr: number;
leads: number;
date: string;
};
export type TagDto = {
id: number;
name: string;
style: string | null;
};
export type TagListDto = {
id: number;
name: string;
style: string;
created_at: string;
};
}
declare namespace App.DTO.Log {
export type ProductLogListDto = {
id: number;
player_id: number;
status: string;
c2d_channel_id: number;
tg_id: number;
prod_id: number;
dep_sum: string;
operator_id: number;
created_at: string;
};
}
declare namespace App.DTO.Meet {
export type MeetRoomDto = {
room: string;
created_at: number;
ttl_remaining: number | null;
};
}
declare namespace App.DTO.Operator {
export type AiRetentionReportDto = {
id: number;
operator_id: number;
client_id: number;
score: number | null;
comment: string;
analysis: string;
raw_payload: Array<any> | null;
conversation_date: string | null;
};
export type AiRetentionReportListDto = {
id: number;
operator_id: number;
client_id: number;
user: App.DTO.User.UserDto | null;
score: number | null;
comment: string;
analysis: string;
raw_payload: Array<any> | null;
conversation_date: string | null;
prompt: string | null;
};
export type OperatorStatisticListDto = {
id: number;
operator_id: number;
new_client_chats: number;
total_clients: number;
inbox_messages: number | null;
outbox_messages: number | null;
total_time: number;
reg_count: number;
dep_count: number;
start_time: string | null;
end_time: string | null;
operator_name: string | null;
operator_score: number | null;
fd: number;
cr_dialog_to_fd: number;
};
export type OperatorStatisticTotalsDto = {
all_clients: number;
all_new_clients: number;
};
}
declare namespace App.DTO.Shorter {
export type DomainDto = {
id: number;
domain: string;
redirect_url: string;
created_at: string;
is_active: boolean;
};
export type UrlListDto = {
id: number;
original_url: string;
short_code: string;
domain: string;
created_at: string;
};
}
declare namespace App.DTO.Ticket {
export type PlayerTicketDto = {
id: number;
ticket_number: string;
user: App.DTO.User.UserDto;
status: string;
player_id: number;
type: string;
tg_id: number;
screen_url: string;
sum: string;
approved_at: string | null;
result: string | null;
created_at: string;
};
export type PlayerTicketListDto = {
id: number;
ticket_number: string;
status: PlayerTicketStatusDto;
user: App.DTO.User.UserDto;
player_id: number;
type: string;
tg_id: number;
is_valid_tg_id: boolean;
screen_url: string;
sum: string;
is_valid_sum: boolean;
approved_at: string | null;
result: string | null;
created_at: string;
comments: CommentDto[];
product_logs: ProductLogListDto[];
};
export type PlayerTicketStatusDto = {
name: string;
color: string;
};
export type PlayerTicketUpdateDto = {
status: string;
result: string | null;
};
export type TicketCategoriesListDto = {
id: number;
name: string;
slug: string;
is_active: boolean;
statuses: Array<TicketStatusDto>;
description: string | null;
sort_order: number | null;
created_at: string | null;
};
export type TicketCategoryDto = {
id: number;
name: string;
slug: string;
is_active: boolean;
};
export type TicketDto = {
id: number;
ticket_number: string;
topic_id: number;
user_id: number;
priority: string;
result: string | null;
approved_at: string | null;
closed_at: string | null;
created_at: string;
status_id: number;
};
export type TicketFieldValuesListDto = {
id: string;
value: string;
formField: TicketFormFieldDto;
};
export type TicketFormFieldDto = {
id: number;
name: string;
label: string;
type: string;
validation_rules: Array<any>;
options: Array<any>;
is_required: boolean;
};
export type TicketFormFieldListDto = {
id: number;
name: string;
label: string;
type: string;
validation_rules: ValidationRuleDto[];
options: Array<any>;
is_required: boolean;
created_at: string | null;
};
export type TicketListAllDto = {
id: number;
ticket_number: string;
topic: TicketTopicListDto;
status: TicketStatusDto;
comments: CommentDto[];
approval: TicketResponsibleUserDto[];
responsible: TicketResponsibleUserDto[];
fieldValues: TicketFieldValuesListDto[];
user: App.DTO.User.UserDto;
priority: string;
result: string | null;
created_at: string;
available_statuses: TicketStatusDto[];
logs: TicketLogDto[];
};
export type TicketListDto = {
id: number;
ticket_number: string;
topic: TicketTopicListDto;
status: TicketStatusDto;
comments: CommentDto[];
approval: TicketResponsibleUserDto[];
responsible: TicketResponsibleUserDto[];
fieldValues: TicketFieldValuesListDto[];
user: App.DTO.User.UserDto;
priority: string;
created_at: string;
available_statuses: TicketStatusDto[];
result: string | null;
};
export type TicketLogDto = {
id: number;
ticket_id: number;
user: App.DTO.User.UserDto;
action: string;
old_values: string;
new_values: string;
created_at: string;
};
export type TicketResponsibleUserDto = {
responsible_title: string | null;
responsible_model_name: string | null;
responsible_id: number | null;
};
export type TicketStatusDto = {
id: number;
name: string;
slug: string;
color: string;
is_default: boolean;
is_final: boolean;
is_approval: boolean;
};
export type TicketStatusesListDto = {
id: number;
name: string;
slug: string;
color: string;
is_default: boolean;
is_final: boolean;
is_approval: boolean;
sort_order: number | null;
created_at: string | null;
};
export type TicketTopicDto = {
id: number;
category_id: number;
name: string;
slug: string;
description: string | null;
fields: TicketFormFieldDto[];
is_active: boolean;
sort_order: number;
};
export type TicketTopicListDto = {
id: number;
category: TicketCategoryDto;
name: string;
slug: string;
description: string | null;
approval: TicketResponsibleUserDto[];
responsible: TicketResponsibleUserDto[];
fields: TicketFormFieldDto[];
is_active: boolean;
sort_order: number;
created_at: string | null;
};
export type ValidationRuleDto = {
type: App.Enums.ValidationRuleType;
value: any;
};
}
declare namespace App.DTO.User {
export type PermissionDto = {
id: number;
name: string;
title: string;
};
export type RoleDto = {
id: number;
name: string;
};
export type UserDto = {
id: number;
name: string;
};
export type UserListDto = {
id: number;
email: string;
name: string;
role: RoleDto;
two_factor: boolean | null;
available_countries: Array<any>;
available_channels: Array<any>;
available_operators: Array<any>;
available_tags: Array<any>;
permissions: Array<PermissionDto>;
last_login: string | null;
password: string | null;
api_token_ids: Array<number>;
};
export type UserOperatorDto = {
operator_id: number | null;
name: string;
};
export type UserProfileDto = {
id: number;
email: string;
name: string;
role: RoleDto;
two_factor: boolean | null;
available_countries: Array<any>;
available_channels: Array<any>;
available_operators: Array<any>;
available_tags: Array<any>;
permissions: Array<PermissionDto>;
last_login_at: string | null;
timezone: string | null;
telegram_connected: boolean;
api_token_ids: Array<number>;
};
}
declare namespace App.Enums {
export enum ApiServiceEnum { 'C2D' = 'Chat2Desk', 'GPT' = 'ChatGPT' };
export enum Chat2DeskEventEnum { 'INBOX' = 'inbox', 'OUTBOX' = 'outbox', 'COMMENT' = 'comment', 'NEW_CLIENT' = 'new_client', 'NEW_REQUEST' = 'new_request', 'ADD_TAG_TO_CLIENT' = 'add_tag_to_client', 'ADD_TAG_TO_REQUEST' = 'add_tag_to_request', 'DELETE_TAG_FROM_CLIENT' = 'delete_tag_from_client', 'DELETE_TAG_FROM_REQUEST' = 'delete_tag_from_request', 'CLIENT_UPDATED' = 'client_updated', 'CLOSE_DIALOG' = 'close_dialog', 'CLOSE_REQUEST' = 'close_request', 'DIALOG_TRANSFERRED' = 'dialog_transferred' };
export enum FieldTypeEnum { 'TEXT' = 'text', 'NUMBER' = 'number', 'SELECT' = 'select', 'MULTISELECT' = 'multiselect', 'COUNTRY' = 'country', 'TEXTAREA' = 'textarea', 'DATE' = 'date', 'FILE' = 'file', 'CHECKBOX' = 'checkbox', 'PROJECT' = 'project' };
export enum PermissionEnum { 'SHORTER_SHOW' = 'shorter.show', 'TICKETS_MODERATE' = 'tickets.moderation', 'TICKETS_SETTINGS' = 'tickets.settings', 'CLIENTS_VIEW' = 'clients.show', 'CREATIVES_CREATE' = 'creatives.create', 'CREATIVES_UPDATE' = 'creatives.update', 'CREATIVES_TAGS' = 'creatives.tags', 'CREATIVES_COMMENTS' = 'creatives.comments', 'OPERATORS_VIEW' = 'operators.show', 'AI_RETENTION_SHOW' = 'ai.retentions.show', 'CHECK_PLAYER_SHOW' = 'check_player.show', 'CHECK_PLAYER_MODERATION' = 'check_player.moderation' };
export enum PlayerTicketStatusEnum { 'ON_APPROVE' = 'On Approve', 'APPROVED' = 'Approved', 'REJECTED' = 'Rejected' };
export enum ReactionTypeEnum { 'LIKE' = 'like', 'DISLIKE' = 'dislike' };
export enum RoleEnum { 'ADMIN' = 'admin', 'MANAGER' = 'manager', 'BUYER' = 'buyer', 'OPERATOR' = 'operator' };
export enum SettingType { 'STRING' = 'string', 'INTEGER' = 'integer', 'BOOLEAN' = 'boolean', 'JSON' = 'json' };
export enum ValidationRuleType { 'Email' = 'email', 'Url' = 'url', 'MaxLength' = 'max_length', 'MinLength' = 'min_length', 'MaxNumber' = 'max_number', 'MinNumber' = 'min_number', 'MinDate' = 'min_date', 'MaxDate' = 'max_date', 'FileType' = 'file_type', 'Contains' = 'contains', 'NotContains' = 'not_contains' };
}
