<template>
	<div class="settings-wrapper">
		<v-container class="py-8">
			<v-row justify="center">
				<v-col cols="12" md="10" lg="8">
					<v-card class="settings-card elevation-1" rounded="lg">
						<!-- Header Section -->
						<div class="card-header pa-6">
							<div class="d-flex align-center justify-space-between">
								<div>
									<h2
										class="text-h5 text-indigo-darken-4 font-weight-bold mb-1"
									>
										Company Settings
									</h2>
									<p class="text-body-2 text-medium-emphasis mb-0">
										Manage your company information and billing preferences
									</p>
								</div>
								<v-icon size="40" color="indigo-darken-4" class="settings-icon">
									mdi-office-building-cog
								</v-icon>
							</div>
						</div>

						<v-divider></v-divider>

						<!-- Form Section -->
						<div class="pa-6">
							<!-- Basic Information -->
							<div class="section-title mb-4">
								<v-icon class="mr-2" color="indigo-darken-4"
									>mdi-information</v-icon
								>
								<span class="text-subtitle-1 font-weight-semibold"
									>Basic Information</span
								>
							</div>

							<v-row>
								<v-col cols="12">
									<v-text-field
										label="Company Name"
										variant="outlined"
										density="comfortable"
										v-model="form.name"
										:disabled="isDisabled"
										prepend-inner-icon="mdi-domain"
										:readonly="isDisabled"
										bg-color="surface"
									/>
								</v-col>

								<v-col cols="12">
									<v-text-field
										label="Address"
										variant="outlined"
										density="comfortable"
										v-model="form.address"
										:disabled="isDisabled"
										prepend-inner-icon="mdi-map-marker"
										:readonly="isDisabled"
										bg-color="surface"
									/>
								</v-col>

								<v-col cols="12">
									<v-text-field
										label="Contact Number"
										variant="outlined"
										density="comfortable"
										v-model="form.contact"
										:disabled="isDisabled"
										prepend-inner-icon="mdi-phone"
										:readonly="isDisabled"
										bg-color="surface"
									/>
								</v-col>
							</v-row>

							<!-- Billing Configuration -->
							<div class="section-title mb-4 mt-6">
								<v-icon class="mr-2" color="indigo-darken-4"
									>mdi-cash-multiple</v-icon
								>
								<span class="text-subtitle-1 font-weight-semibold"
									>Billing Configuration</span
								>
							</div>

							<v-row>
								<v-col cols="12">
									<v-select
										label="Rate Type"
										variant="outlined"
										density="comfortable"
										:items="types"
										item-title="label"
										item-value="value"
										v-model="form.rate"
										:disabled="isDisabled"
										prepend-inner-icon="mdi-clipboard-text-clock"
										:readonly="isDisabled"
										bg-color="surface"
									>
										<template v-slot:item="{ props, item }">
											<v-list-item v-bind="props">
												<template v-slot:prepend>
													<v-icon :icon="getRateIcon(item.raw.value)"></v-icon>
												</template>
											</v-list-item>
										</template>
									</v-select>
								</v-col>

								<v-col cols="12" sm="6">
									<v-text-field
										label="Rate per Hour"
										variant="outlined"
										density="comfortable"
										v-model="form.rate_perhour"
										:disabled="
											isDisabled ||
											(form.rate !== 'perhour' && form.rate !== 'combination')
										"
										type="number"
										:readonly="
											isDisabled ||
											(form.rate !== 'perhour' && form.rate !== 'combination')
										"
										bg-color="surface"
										prefix="₱"
									/>
								</v-col>

								<v-col cols="12" sm="6">
									<v-text-field
										label="Rate per Day"
										variant="outlined"
										density="comfortable"
										v-model="form.rate_perday"
										:disabled="
											isDisabled ||
											(form.rate !== 'perday' && form.rate !== 'combination')
										"
										type="number"
										:readonly="
											isDisabled ||
											(form.rate !== 'perday' && form.rate !== 'combination')
										"
										bg-color="surface"
										prefix="₱"
									/>
								</v-col>
							</v-row>

							<!-- Combination Rate Settings -->
							<v-expand-transition>
								<div v-if="form.rate === 'combination'">
									<v-alert
										type="info"
										variant="tonal"
										class="mb-4"
										density="compact"
										icon="mdi-information-outline"
									>
										Configure how hourly and daily rates combine
									</v-alert>

									<v-row>
										<v-col cols="12" sm="6">
											<v-text-field
												label="Hourly Billing Limit"
												variant="outlined"
												density="comfortable"
												v-model="form.hourly_billing_limit"
												:disabled="isDisabled"
												type="number"
												min="1"
												suffix="hours"
												prepend-inner-icon="mdi-timer-sand"
												hint="Hours charged before switching to daily rate"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>

										<v-col cols="12" sm="6">
											<v-text-field
												label="Grace Period"
												variant="outlined"
												density="comfortable"
												v-model="form.grace_minutes"
												:disabled="isDisabled"
												type="number"
												min="0"
												suffix="minutes"
												prepend-inner-icon="mdi-clock-plus-outline"
												hint="Free minutes after 24 hours"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>

										<v-col cols="12" sm="6">
											<v-text-field
												label="Additional Hour Block"
												variant="outlined"
												density="comfortable"
												v-model="form.additional_hour_block"
												:disabled="isDisabled"
												type="number"
												min="1"
												suffix="hours"
												prepend-inner-icon="mdi-clock-fast"
												hint="Billing block size after 24 hours (e.g., 3)"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>

										<v-col cols="12" sm="6">
											<v-text-field
												label="Rate per Block"
												variant="outlined"
												density="comfortable"
												v-model="form.additional_rate_per_block"
												:disabled="isDisabled"
												type="number"
												min="0"
												step="0.01"
												prefix="₱"
												prepend-inner-icon="mdi-cash-plus"
												hint="Charge per block after 24 hours (e.g., ₱50)"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>
									</v-row>

									<!-- Billing Example Display -->
									<v-card
										variant="outlined"
										class="mt-4 pa-4 bg-grey-lighten-5"
									>
										<div
											class="text-subtitle-2 font-weight-semibold mb-2 text-indigo-darken-4"
										>
											<v-icon size="small" class="mr-1">mdi-calculator</v-icon>
											Billing Example
										</div>
										<div class="text-body-2">
											<ul class="ml-4">
												<li>
													<strong
														>0-{{
															form.hourly_billing_limit || 12
														}}
														hours:</strong
													>
													₱{{ form.rate_perhour || 20 }}/hour
												</li>
												<li>
													<strong
														>{{ form.hourly_billing_limit || 12 }}-24
														hours:</strong
													>
													₱{{ form.rate_perday || 350 }} (daily rate)
												</li>
												<li>
													<strong
														>25-{{
															24 + parseInt(form.additional_hour_block || 3)
														}}
														hours:</strong
													>
													₱{{ form.rate_perday || 350 }} (free
													{{ form.additional_hour_block || 3 }}-hour grace
													block)
												</li>
												<li>
													<strong
														>{{
															24 +
															parseInt(form.additional_hour_block || 3) +
															1
														}}+ hours:</strong
													>
													₱{{ form.rate_perday || 350 }} + ₱{{
														form.additional_rate_per_block || 50
													}}
													per {{ form.additional_hour_block || 3 }}-hour block
												</li>
											</ul>
										</div>
									</v-card>
								</div>
							</v-expand-transition>

							<!-- Grace Period Display for Per Day only -->
							<v-expand-transition>
								<div v-if="form.rate === 'perday'">
									<v-row>
										<v-col cols="12">
											<v-text-field
												label="Grace Period"
												variant="outlined"
												density="comfortable"
												v-model="form.grace_minutes"
												:disabled="isDisabled"
												type="number"
												min="0"
												suffix="minutes"
												prepend-inner-icon="mdi-clock-plus-outline"
												hint="Free minutes allowed beyond daily limit"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>
									</v-row>
								</div>
							</v-expand-transition>

							<!-- ─── Motor Billing Configuration ─── -->
							<div class="section-title mb-4 mt-6">
								<v-icon class="mr-2" color="indigo-darken-4"
									>mdi-motorbike</v-icon
								>
								<span class="text-subtitle-1 font-weight-semibold"
									>Motor Billing Configuration</span
								>
							</div>

							<v-row>
								<v-col cols="12">
									<v-select
										label="Motor Rate Type"
										variant="outlined"
										density="comfortable"
										:items="types"
										item-title="label"
										item-value="value"
										v-model="form.motor_rate"
										:disabled="isDisabled"
										prepend-inner-icon="mdi-clipboard-text-clock"
										:readonly="isDisabled"
										bg-color="surface"
									>
										<template v-slot:item="{ props, item }">
											<v-list-item v-bind="props">
												<template v-slot:prepend>
													<v-icon :icon="getRateIcon(item.raw.value)"></v-icon>
												</template>
											</v-list-item>
										</template>
									</v-select>
								</v-col>

								<v-col cols="12" sm="6">
									<v-text-field
										label="Motor Rate per Hour"
										variant="outlined"
										density="comfortable"
										v-model="form.motor_rate_perhour"
										:disabled="
											isDisabled ||
											(form.motor_rate !== 'perhour' &&
												form.motor_rate !== 'combination')
										"
										type="number"
										:readonly="
											isDisabled ||
											(form.motor_rate !== 'perhour' &&
												form.motor_rate !== 'combination')
										"
										bg-color="surface"
										prefix="₱"
										prepend-inner-icon="mdi-clock-outline"
									/>
								</v-col>

								<v-col cols="12" sm="6">
									<v-text-field
										label="Motor Rate per Day"
										variant="outlined"
										density="comfortable"
										v-model="form.motor_rate_perday"
										:disabled="
											isDisabled ||
											(form.motor_rate !== 'perday' &&
												form.motor_rate !== 'combination')
										"
										type="number"
										:readonly="
											isDisabled ||
											(form.motor_rate !== 'perday' &&
												form.motor_rate !== 'combination')
										"
										bg-color="surface"
										prefix="₱"
										prepend-inner-icon="mdi-calendar-today"
									/>
								</v-col>
							</v-row>

							<!-- Motor Combination Rate Settings -->
							<v-expand-transition>
								<div v-if="form.motor_rate === 'combination'">
									<v-alert
										type="info"
										variant="tonal"
										class="mb-4"
										density="compact"
										icon="mdi-information-outline"
									>
										Configure how motor hourly and daily rates combine
									</v-alert>

									<v-row>
										<v-col cols="12" sm="6">
											<v-text-field
												label="Motor Hourly Billing Limit"
												variant="outlined"
												density="comfortable"
												v-model="form.motor_hourly_billing_limit"
												:disabled="isDisabled"
												type="number"
												min="1"
												suffix="hours"
												prepend-inner-icon="mdi-timer-sand"
												hint="Hours charged before switching to daily rate"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>

										<v-col cols="12" sm="6">
											<v-text-field
												label="Motor Grace Period"
												variant="outlined"
												density="comfortable"
												v-model="form.motor_grace_minutes"
												:disabled="isDisabled"
												type="number"
												min="0"
												suffix="minutes"
												prepend-inner-icon="mdi-clock-plus-outline"
												hint="Free minutes after 24 hours"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>

										<v-col cols="12" sm="6">
											<v-text-field
												label="Motor Additional Hour Block"
												variant="outlined"
												density="comfortable"
												v-model="form.motor_additional_hour_block"
												:disabled="isDisabled"
												type="number"
												min="1"
												suffix="hours"
												prepend-inner-icon="mdi-clock-fast"
												hint="Billing block size after 24 hours (e.g., 3)"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>

										<v-col cols="12" sm="6">
											<v-text-field
												label="Motor Rate per Block"
												variant="outlined"
												density="comfortable"
												v-model="form.motor_additional_rate_per_block"
												:disabled="isDisabled"
												type="number"
												min="0"
												step="0.01"
												prefix="₱"
												prepend-inner-icon="mdi-cash-plus"
												hint="Charge per block after 24 hours (e.g., ₱50)"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>
									</v-row>

									<!-- Motor Billing Example Display -->
									<v-card
										variant="outlined"
										class="mt-4 pa-4 bg-grey-lighten-5"
									>
										<div
											class="text-subtitle-2 font-weight-semibold mb-2 text-indigo-darken-4"
										>
											<v-icon size="small" class="mr-1">mdi-calculator</v-icon>
											Motor Billing Example
										</div>
										<div class="text-body-2">
											<ul class="ml-4">
												<li>
													<strong
														>0-{{
															form.motor_hourly_billing_limit || 12
														}}
														hours:</strong
													>
													₱{{ form.motor_rate_perhour || 10 }}/hour
												</li>
												<li>
													<strong
														>{{ form.motor_hourly_billing_limit || 12 }}-24
														hours:</strong
													>
													₱{{ form.motor_rate_perday || 150 }} (daily rate)
												</li>
												<li>
													<strong
														>25-{{
															24 +
															parseInt(form.motor_additional_hour_block || 3)
														}}
														hours:</strong
													>
													₱{{ form.motor_rate_perday || 150 }} (free
													{{ form.motor_additional_hour_block || 3 }}-hour grace
													block)
												</li>
												<li>
													<strong
														>{{
															24 +
															parseInt(form.motor_additional_hour_block || 3) +
															1
														}}+ hours:</strong
													>
													₱{{ form.motor_rate_perday || 150 }} + ₱{{
														form.motor_additional_rate_per_block || 25
													}}
													per {{ form.motor_additional_hour_block || 3 }}-hour
													block
												</li>
											</ul>
										</div>
									</v-card>
								</div>
							</v-expand-transition>

							<!-- Motor Grace Period for Per Day only -->
							<v-expand-transition>
								<div v-if="form.motor_rate === 'perday'">
									<v-row>
										<v-col cols="12">
											<v-text-field
												label="Motor Grace Period"
												variant="outlined"
												density="comfortable"
												v-model="form.motor_grace_minutes"
												:disabled="isDisabled"
												type="number"
												min="0"
												suffix="minutes"
												prepend-inner-icon="mdi-clock-plus-outline"
												hint="Free minutes allowed beyond daily limit"
												persistent-hint
												:readonly="isDisabled"
												bg-color="surface"
											/>
										</v-col>
									</v-row>
								</div>
							</v-expand-transition>
						</div>

						<v-divider></v-divider>

						<!-- Action Buttons -->
						<div class="pa-6">
							<div class="d-flex justify-end ga-3">
								<v-btn
									v-if="!isDisabled"
									variant="outlined"
									@click="cancelEdit"
									prepend-icon="mdi-close"
									size="large"
									color="indigo-darken-4"
								>
									Cancel
								</v-btn>

								<v-btn
									v-if="isDisabled"
									color="primary"
									@click="isDisabled = false"
									prepend-icon="mdi-pencil"
									size="large"
									class="px-6"
								>
									Edit Settings
								</v-btn>

								<v-btn
									v-else
									color="indigo-darken-4"
									@click="updateCompany"
									prepend-icon="mdi-content-save"
									size="large"
									class="px-6"
									:loading="form.processing"
								>
									Save Changes
								</v-btn>
							</div>
						</div>
					</v-card>
				</v-col>
			</v-row>
		</v-container>
	</div>
</template>

<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const page = usePage();
const company = page.props.company;

const isDisabled = ref(true);

const originalValues = {
	name: company.name,
	address: company.address,
	contact: company.contact,
	rate: company.rate,
	rate_perhour: company.rate_perhour,
	rate_perday: company.rate_perday,
	hourly_billing_limit: company.hourly_billing_limit,
	grace_minutes: company.grace_minutes,
	additional_hour_block: company.additional_hour_block,
	additional_rate_per_block: company.additional_rate_per_block,
	// Motor fields
	motor_rate: company.motor_rate,
	motor_rate_perhour: company.motor_rate_perhour,
	motor_rate_perday: company.motor_rate_perday,
	motor_hourly_billing_limit: company.motor_hourly_billing_limit,
	motor_grace_minutes: company.motor_grace_minutes,
	motor_additional_hour_block: company.motor_additional_hour_block,
	motor_additional_rate_per_block: company.motor_additional_rate_per_block,
};

const form = useForm({
	id: company.id,
	name: company.name,
	address: company.address,
	contact: company.contact,
	rate: company.rate,
	rate_perhour: company.rate_perhour,
	rate_perday: company.rate_perday,
	hourly_billing_limit: company.hourly_billing_limit,
	grace_minutes: company.grace_minutes,
	additional_hour_block: company.additional_hour_block,
	additional_rate_per_block: company.additional_rate_per_block,
	// Motor fields
	motor_rate: company.motor_rate,
	motor_rate_perhour: company.motor_rate_perhour,
	motor_rate_perday: company.motor_rate_perday,
	motor_hourly_billing_limit: company.motor_hourly_billing_limit,
	motor_grace_minutes: company.motor_grace_minutes,
	motor_additional_hour_block: company.motor_additional_hour_block,
	motor_additional_rate_per_block: company.motor_additional_rate_per_block,
});

const types = [
	{ label: "Per Hour", value: "perhour" },
	{ label: "Per Day", value: "perday" },
	{ label: "Combination", value: "combination" },
];

const getRateIcon = (value) => {
	switch (value) {
		case "perhour":
			return "mdi-clock-outline";
		case "perday":
			return "mdi-calendar-today";
		case "combination":
			return "mdi-alarm-multiple";
		default:
			return "mdi-cash";
	}
};

const cancelEdit = () => {
	Object.keys(originalValues).forEach((key) => {
		form[key] = originalValues[key];
	});
	isDisabled.value = true;
};

const updateCompany = () => {
	form.put(route("company.update", form.id), {
		preserveScroll: true,
		onSuccess: () => {
			isDisabled.value = true;
			Object.keys(originalValues).forEach((key) => {
				originalValues[key] = form[key];
			});
			console.log("✅ Company updated successfully!");
		},
		onError: (errors) => {
			console.error("❌ Validation failed:", errors);
		},
	});
};
</script>

<style scoped>
.settings-wrapper {
	min-height: 100vh;
}

.settings-card {
	overflow: hidden;
	transition: transform 0.2s ease;
}

.card-header {
	background: linear-gradient(
		135deg,
		rgba(102, 126, 234, 0.1) 0%,
		rgba(118, 75, 162, 0.1) 100%
	);
}

.settings-icon {
	opacity: 0.8;
}

.section-title {
	display: flex;
	align-items: center;
	padding-bottom: 8px;
	border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

:deep(.v-field--focused) {
	box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

:deep(.v-field),
:deep(.v-btn) {
	transition: all 0.2s ease;
}

:deep(.v-field--disabled) {
	opacity: 0.7;
}

.v-btn:hover {
	transform: translateY(-1px);
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
