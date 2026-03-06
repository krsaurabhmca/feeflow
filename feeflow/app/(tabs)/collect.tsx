import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, ScrollView, TextInput, TouchableOpacity, Alert, ActivityIndicator, Linking } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { getStudents, getClasses, collectFee } from '../../lib/api';
import * as Print from 'expo-print';
import * as Sharing from 'expo-sharing';

export default function CollectFeeScreen() {
    const [students, setStudents] = useState<any[]>([]);
    const [selectedStudent, setSelectedStudent] = useState<any>(null);
    const [amount, setAmount] = useState('');
    const [remarks, setRemarks] = useState('');
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState('');

    const searchStudents = async (query: string) => {
        setSearch(query);
        if (query.length > 2) {
            const resp = await getStudents(query);
            setStudents(resp.data);
        } else {
            setStudents([]);
        }
    };

    const handleCollect = async () => {
        if (!selectedStudent || !amount) {
            Alert.alert('Error', 'Please select a student and enter amount');
            return;
        }

        setLoading(true);
        try {
            const response = await collectFee({
                student_id: selectedStudent.id,
                amount: parseFloat(amount),
                remarks: remarks,
                payment_method: 'App Payment'
            });

            if (response.status) {
                Alert.alert(
                    'Success',
                    `Fee collected! Receipt: ${response.data.receipt_no}`,
                    [
                        { text: 'Share to WhatsApp', onPress: () => shareReceipt(response.data) },
                        { text: 'Done', onPress: resetForm }
                    ]
                );
            }
        } catch (e) {
            Alert.alert('Error', 'Failed to collect fee');
        } finally {
            setLoading(false);
        }
    };

    const shareReceipt = async (receiptData: any) => {
        const html = `
      <html>
        <body style="font-family: sans-serif; padding: 40px; text-align: center;">
          <h1 style="color: #dc2626;">Fee Receipt</h1>
          <hr>
          <div style="text-align: left;">
            <p><strong>Receipt No:</strong> ${receiptData.receipt_no}</p>
            <p><strong>Student:</strong> ${selectedStudent.name}</p>
            <p><strong>Amount:</strong> ₹${amount}</p>
            <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
          </div>
          <hr>
          <p>Thank you for your payment!</p>
        </body>
      </html>
    `;

        try {
            const { uri } = await Print.printToFileAsync({ html });
            await Sharing.shareAsync(uri);

            // Direct WhatsApp link if phone is available
            if (selectedStudent.phone) {
                const message = `High, your payment of ₹${amount} for ${selectedStudent.name} has been received. Receipt No: ${receiptData.receipt_no}`;
                Linking.openURL(`whatsapp://send?phone=91${selectedStudent.phone}&text=${encodeURIComponent(message)}`);
            }
        } catch (e) {
            console.error(e);
        }
    };

    const resetForm = () => {
        setSelectedStudent(null);
        setAmount('');
        setRemarks('');
        setSearch('');
        setStudents([]);
    };

    return (
        <ScrollView style={styles.container} keyboardShouldPersistTaps="handled">
            <View style={styles.form}>
                <Text style={styles.label}>Search Student</Text>
                <TextInput
                    style={styles.input}
                    placeholder="Type name or roll no..."
                    value={search}
                    onChangeText={searchStudents}
                />

                {students.length > 0 && !selectedStudent && (
                    <View style={styles.suggestions}>
                        {students.map(s => (
                            <TouchableOpacity
                                key={s.id}
                                style={styles.suggestionItem}
                                onPress={() => {
                                    setSelectedStudent(s);
                                    setSearch(s.name);
                                    setStudents([]);
                                }}
                            >
                                <Text style={styles.suggestionText}>{s.name} ({s.roll_no})</Text>
                            </TouchableOpacity>
                        ))}
                    </View>
                )}

                {selectedStudent && (
                    <View style={styles.selectedBox}>
                        <Text style={styles.selectedTitle}>Selected Student</Text>
                        <Text style={styles.selectedName}>{selectedStudent.name}</Text>
                        <Text style={styles.selectedMeta}>{selectedStudent.class_name} • {selectedStudent.roll_no}</Text>
                    </View>
                )}

                <Text style={styles.label}>Amount (₹)</Text>
                <TextInput
                    style={styles.input}
                    placeholder="0.00"
                    value={amount}
                    onChangeText={setAmount}
                    keyboardType="numeric"
                />

                <Text style={styles.label}>Remarks</Text>
                <TextInput
                    style={[styles.input, { height: 100 }]}
                    placeholder="Optional notes..."
                    value={remarks}
                    onChangeText={setRemarks}
                    multiline
                />

                <TouchableOpacity
                    style={[styles.button, loading && { opacity: 0.7 }]}
                    onPress={handleCollect}
                    disabled={loading}
                >
                    {loading ? (
                        <ActivityIndicator color="white" />
                    ) : (
                        <>
                            <Ionicons name="checkmark-circle" size={20} color="white" style={{ marginRight: 10 }} />
                            <Text style={styles.buttonText}>Collect & Generate Receipt</Text>
                        </>
                    )}
                </TouchableOpacity>
            </View>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    form: {
        padding: 20,
        gap: 15,
    },
    label: {
        fontWeight: '700',
        color: '#1e293b',
        fontSize: 14,
    },
    input: {
        borderWidth: 1,
        borderColor: '#e2e8f0',
        borderRadius: 12,
        padding: 15,
        fontSize: 16,
        backgroundColor: '#f8fafc',
    },
    suggestions: {
        backgroundColor: '#fff',
        borderRadius: 12,
        borderWidth: 1,
        borderColor: '#e2e8f0',
        marginTop: -10,
        maxHeight: 200,
    },
    suggestionItem: {
        padding: 15,
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    suggestionText: {
        color: '#1e293b',
        fontWeight: '600',
    },
    selectedBox: {
        padding: 15,
        backgroundColor: '#fff1f2',
        borderRadius: 12,
        borderWidth: 1,
        borderColor: '#fecdd3',
    },
    selectedTitle: {
        fontSize: 12,
        color: '#dc2626',
        fontWeight: '700',
        marginBottom: 5,
    },
    selectedName: {
        fontSize: 18,
        fontWeight: '800',
        color: '#dc2626',
    },
    selectedMeta: {
        fontSize: 12,
        color: '#991b1b',
    },
    button: {
        backgroundColor: '#dc2626',
        borderRadius: 12,
        padding: 18,
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        marginTop: 10,
    },
    buttonText: {
        color: 'white',
        fontWeight: '800',
        fontSize: 16,
    },
});
