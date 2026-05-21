import api from "@/services/axiosService";

export const absensiService = {
  async getToday() {
    const response = await api.get("/absensi/today");
    return response.data;
  },

  async absenDatang() {
    const response = await api.post("/absensi/datang");
    return response.data;
  },

  async absenPulang() {
    const response = await api.post("/absensi/pulang");
    return response.data;
  },
};
