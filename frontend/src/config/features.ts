// Feature flags for controlling which features are visible/enabled
const features = {
  // Enable Xendit disbursement (banks/e-wallets like BCA, BNI, BRI, OVO, Dana, GoPay)
  enableXenditDisbursement: import.meta.env.VITE_ENABLE_XENDIT_DISBURSEMENT === "true",
};

export function isFeatureEnabled(key: keyof typeof features): boolean {
  return features[key] === true;
}

export { features };
