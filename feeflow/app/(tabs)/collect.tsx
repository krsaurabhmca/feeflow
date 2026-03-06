import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, ScrollView, TextInput, TouchableOpacity, Alert, ActivityIndicator, Linking } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { getStudents, getClasses, collectFee, getCategories } from '../../lib/api';
import * as Print from 'expo-print';
import * as Sharing from 'expo-sharing';
import { useLocalSearchParams } from 'expo-router';

export default function CollectFeeScreen() {
    const params = useLocalSearchParams();
    const [students, setStudents] = useState<any[]>([]);
    const [selectedStudent, setSelectedStudent] = useState<any>(null);
    const [amount, setAmount] = useState('');
    const [remarks, setRemarks] = useState('');
    const [categories, setCategories] = useState<any[]>([]);
    const [selectedCat, setSelectedCat] = useState<string | null>(null);
    const [paymentDate, setPaymentDate] = useState(new Date().toISOString().split('T')[0]);
    const [paymentMethod, setPaymentMethod] = useState('Cash');
    const [customFeeName, setCustomFeeName] = useState('');
    const [loading, setLoading] = useState(false);
    const [search, setSearch] = useState('');

    useEffect(() => {
        getCategories().then(res => {
            if (res.status) setCategories(res.data);
        });

        if (params.student_id) {
            setSelectedStudent({ id: params.student_id, name: params.name });
            setSearch(params.name as string);
        }
    }, [params]);


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
                fee_category_id: selectedCat,
                payment_date: paymentDate,
                payment_method: paymentMethod,
                custom_fee_name: customFeeName
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
        <head>
          <style>
            body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; padding: 20px; background-color: #f1f5f9; }
            .receipt { background-color: #fff; padding: 40px; border-radius: 20px; max-width: 500px; margin: auto; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
            .header { text-align: center; margin-bottom: 30px; }
            .header h1 { color: #dc2626; margin: 0; font-size: 28px; }
            .header p { color: #64748b; margin: 5px 0; }
            .divider { border-top: 2px dashed #e2e8f0; margin: 25px 0; }
            .item { display: flex; justify-content: space-between; margin-bottom: 12px; }
            .label { color: #64748b; font-weight: 500; }
            .value { color: #1e293b; font-weight: 700; text-align: right; }
            .total-box { background-color: #dc2626; color: #fff; padding: 20px; border-radius: 12px; text-align: center; margin-top: 20px; }
            .total-label { font-size: 14px; opacity: 0.9; }
            .total-value { font-size: 32px; font-weight: 800; margin-top: 5px; }
            .footer { text-align: center; margin-top: 30px; color: #94a3b8; font-size: 12px; }
          </style>
        </head>
        <body>
          <div class="receipt">
            <div class="header">
              <h1>FeeFlow Receipt</h1>
              <p>Professional Fee Management</p>
            </div>
            
            <div class="item">
              <span class="label">Receipt No</span>
              <span class="value">${receiptData.receipt_no}</span>
            </div>
            <div class="item">
              <span class="label">Date</span>
              <span class="value">${paymentDate}</span>
            </div>

            <div class="divider"></div>

            <div class="item">
              <span class="label">Student Name</span>
              <span class="value">${selectedStudent.name}</span>
            </div>
            <div class="item">
              <span class="label">Class/Roll</span>
              <span class="value">${selectedStudent.class_name} / ${selectedStudent.roll_no}</span>
            </div>
            <div class="item">
              <span class="label">Fee Category</span>
              <span class="value">${customFeeName || 'General Fee'}</span>
            </div>
             <div class="item">
              <span class="label">Payment Mode</span>
              <span class="value">${paymentMethod}</span>
            </div>

            <div class="total-box">
              <div class="total-label">Amount Paid</div>
              <div class="total-value">₹${amount}</div>
            </div>

            <div class="divider"></div>
            
            <div class="footer">
              <p>This is a computer-generated receipt.</p>
              <p>Thank you for your payment!</p>
            </div>
          </div>
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
        setPaymentDate(new Date().toISOString().split('T')[0]);
        setPaymentMethod('Cash');
        setCustomFeeName('');
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

                <Text style={styles.label}>Fee Category</Text>
                <View style={styles.classGrid}>
                    {categories.map((c) => (
                        <TouchableOpacity
                            key={c.id}
                            style={[styles.classItem, selectedCat === c.id.toString() && styles.classSelected]}
                            onPress={() => {
                                setSelectedCat(c.id.toString());
                                if (!customFeeName) setCustomFeeName(c.category_name);
                            }}
                        >
                            <Text style={[styles.classText, selectedCat === c.id.toString() && styles.classTextSelected]}>
                                {c.category_name}
                            </Text>
                        </TouchableOpacity>
                    ))}
                </View>

                <Text style={styles.label}>Custom Fee Name (Optional)</Text>
                <TextInput
                    style={styles.input}
                    placeholder="e.g. Admission Fee, Monthly Fee"
                    value={customFeeName}
                    onChangeText={setCustomFeeName}
                />

                <View style={styles.row}>
                    <View style={{ flex: 1 }}>
                        <Text style={styles.label}>Date</Text>
                        <TextInput
                            style={styles.input}
                            placeholder="YYYY-MM-DD"
                            value={paymentDate}
                            onChangeText={setPaymentDate}
                        />
                    </View>
                    <View style={{ flex: 1 }}>
                        <Text style={styles.label}>Payment Mode</Text>
                        <TextInput
                            style={styles.input}
                            placeholder="Cash, Online, etc."
                            value={paymentMethod}
                            onChangeText={setPaymentMethod}
                        />
                    </View>
                </View>

                <Text style={styles.label}>Remarks</Text>
                <TextInput
                    style={[styles.input, { height: 80 }]}
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
        gap: 12,
    },
    row: {
        flexDirection: 'row',
        gap: 10,
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
    classGrid: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: 8,
    },
    classItem: {
        paddingVertical: 8,
        paddingHorizontal: 12,
        borderRadius: 8,
        backgroundColor: '#f1f5f9',
        borderWidth: 1,
        borderColor: '#e2e8f0',
    },
    classSelected: {
        backgroundColor: '#dc2626',
        borderColor: '#dc2626',
    },
    classText: {
        color: '#475569',
        fontWeight: '600',
        fontSize: 12,
    },
    classTextSelected: {
        color: '#fff',
    },
});

