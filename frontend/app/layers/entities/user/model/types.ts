/** Item of `GET /users`. */
export interface UserListItem {
  id: string
  email: string
  name: string
  role: { id: string, name: string }
  must_change_password: boolean
  created_at: string
}

/** Item of `GET /users/assignable-roles` (every Role except Visiter). */
export interface RoleOption {
  id: string
  name: string
}

export interface CreateUserInput {
  email: string
  name: string
  role_id: string
}

/** Payload of `POST /users`; `mail_sent` is false when the credentials email could not be delivered. */
export interface CreateUserResult {
  id: string
  mail_sent: boolean
}

/** Body of `PUT /users/{id}`. */
export type UpdateUserInput = CreateUserInput

/** Payload of `POST /users/{id}/reset-password`; `mail_sent` is false when the email could not be delivered. */
export interface ResetPasswordResult {
  mail_sent: boolean
}
